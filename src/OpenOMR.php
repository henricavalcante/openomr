<?php
namespace OpenOMR;

use Imagick;
use ImagickPixel;

class OpenOMR
{
    const EDGE = 4;
    const DEBUG = 0;
    const MATRIXROWS = 54;
    const MATRIXCOLS = 38;
    const ERROR_MARGIN = 0.5;

    private $debugFolder;

    private $img;
    private $imgSizeW;
    private $imgSizeH;
    private $imgCelSizeW;
    private $imgCelSizeH;
    private $imgCelCompareSizeW;
    private $imgCelCompareSizeH;

    public function __construct($path)
    {
        $this->img = new Imagick();
        $this->img->readImage($path);

        if (self::DEBUG) {
            $this->debugFolder = getdate()[0] . '/';
            mkdir($this->debugFolder);
        }

        $this->removeColors();
        if (self::DEBUG) {
            $this->img->writeImage($this->debugFolder . 'removeColors.PNG');
        }
        $this->adjustImage();
        if (self::DEBUG) {
            $this->img->writeImage($this->debugFolder . 'adjustImage.PNG');
        }
        $this->removeEdges();
        if (self::DEBUG) {
            $this->img->writeImage($this->debugFolder . 'removeEdges.PNG');
        }
    }

    private function removeColors()
    {
        $imginfo = $this->img->identifyImage();
        if ($imginfo['type'] == 'TrueColor') {
            // remove red from image
            $this->img->separateImageChannel(Imagick::CHANNEL_RED);
        } else {
            // desaturate
            $this->img->modulateImage(100, 0, 100);
        }
    }

    private function adjustImage()
    {
        $this->img->normalizeImage(Imagick::CHANNEL_ALL);
        $this->img->enhanceImage();
        $this->img->despeckleImage();

        $this->img->blackthresholdImage('#808080');
        $this->img->whitethresholdImage('#808080');
    }

    private function removeEdges()
    {

        //remove edges and possible skew from image
        $this->img->trimImage(85);
        $this->img->deskewImage(15);
        $this->img->trimImage(85);
        $this->img->setImagePage(0, 0, 0, 0);

        $this->calculateSizes();
    }

    private function calculateSizes()
    {

        $imginfo = $this->img->identifyImage();
        $this->imgSizeW = $imginfo['geometry']['width'];
        $this->imgSizeH = $imginfo['geometry']['height'];

        $this->imgCelSizeW = $this->imgSizeW / self::MATRIXCOLS;
        $this->imgCelSizeH = $this->imgSizeH / self::MATRIXROWS;

        $this->imgCelCompareSizeW = $this->imgCelSizeW - (self::EDGE * 2);
        $this->imgCelCompareSizeH = $this->imgCelSizeH - (self::EDGE * 2);

        if (self::DEBUG) {
            print_r($this);
        }

    }

    private function getRegionFromImage($row, $col)
    {
        $sizeW = $this->imgCelCompareSizeW;
        $sizeH = $this->imgCelCompareSizeH;
        $pX = ($col * $this->imgCelSizeW) + self::EDGE;
        $pY = ($row * $this->imgCelSizeW) + self::EDGE;

        if (self::DEBUG) {
            echo "\n" . $col . " - " . $row . " - " . $pX . " - " . $pY;
        }

        return $this->img->getImageRegion($sizeW, $sizeH, $pX, $pY);
    }

    public function getMatrixFromImage()
    {

        // create reference block pattern
        $imageToCompare = new Imagick();
        $imageToCompare->newImage($this->imgCelCompareSizeW, $this->imgCelCompareSizeH, new ImagickPixel('black'));

        $matrixResult = [];

        for ($i = 0; $i < self::MATRIXROWS; $i++) {

            for ($j = 0; $j < self::MATRIXCOLS; $j++) {
                if (!isset($matrixResult[$i])) {
                    $matrixResult[$i] = [];
                }


                $regionToCompare = $this->getRegionFromImage($i, $j);

                $regionToCompare->setImagePage(0, 0, 0, 0);

                if (self::DEBUG) {
                    $regionToCompare->writeImage($this->debugFolder . $i . '-' . $j . '.PNG');
                }

                $imageCompared = $regionToCompare->compareImages($imageToCompare, Imagick::METRIC_ROOTMEANSQUAREDERROR);

                $matrixResult[$i][$j] = $imageCompared[1];

                $regionToCompare->clear();
            }
        }

        $imageToCompare->clear();

        return $matrixResult;
    }


    public function getMarksFromPaths($paths)
    {
        // create reference block pattern
        $imageToCompare = new Imagick();
        $imageToCompare->newImage($this->imgCelCompareSizeW, $this->imgCelCompareSizeH, new ImagickPixel('black'));

        $result = [];

        foreach ($paths as $path) {

            if (!isset($result[$path['field']])) {
                $result[$path['field']] = ['status' => 0, 'value' => '', 'error_margin' => 1];
            } else {
                if ($result[$path['field']]['status'] == 3) {
                    //if marked with wrong dont search next path
                    continue;
                } else {
                    if ($result[$path['field']]['status'] == 2) {
                        //if a next char from field reset status
                        $result[$path['field']]['status'] = 0;
                    }
                }
            }

            foreach ($path['marks'] as $mark) {

                if ($result[$path['field']]['status'] == 0) {
                    $result[$path['field']]['status'] = 1;
                }

                $regionToCompare = $this->getRegionFromImage($mark[0], $mark[1]);

                $regionToCompare->setImagePage(0, 0, 0, 0);

                if (self::DEBUG) {
                    $regionToCompare->writeImage($this->debugFolder . $mark[0] . '-' . $mark[1] . '.PNG');
                }

                $differenceBetweenImages = $regionToCompare->compareImages($imageToCompare,
                    Imagick::METRIC_ROOTMEANSQUAREDERROR);

                if ($differenceBetweenImages[1] < $result[$path['field']]['error_margin']) {
                    $result[$path['field']]['error_margin'] = $differenceBetweenImages[1];
                }

                if ($differenceBetweenImages[1] < self::ERROR_MARGIN) {
                    //cade doesn't exists difference between black square and region marked concatenate the value
                    $result[$path['field']]['value'] .= $mark[2];

                    if ($result[$path['field']]['status'] == 2) {
                        $result[$path['field']]['status'] = 3;
                    } else {
                        $result[$path['field']]['status'] = 2;
                    }
                };

                $regionToCompare->clear();

            }
        }

        $imageToCompare->clear();

        return $result;
    }

}
