<?php
namespace OpenOMR;

use OpenOMR\Exception;
use Imagick;
use ImagickPixel;
use ImagickException;

class Reader
{
    const DEBUG = 0;
    const ERROR_MARGIN = 0.5;

    private $debugFolder;

    private $img;
    private $imgSizeW;
    private $imgSizeH;
    private $imgCellSizeW;
    private $imgCellSizeH;
    private $imgCellCompareSizeW;
    private $imgCellCompareSizeH;
    private $imgCellOffset;
    private $matrixXLength;
    private $matrixYLength;

    public function __construct($imgPath, $matrixXLength = null, $matrixYLength = null, $imgCellOffset = null)
    {
        if (!extension_loaded('imagick')) {
            throw new Exception\ImagickExtensionNotFoundException('Imagick extension is not loaded.');
        }

        $this->createImageFromPath($imgPath);

        $this->setMatrixXLength($matrixXLength);
        $this->setMatrixYLength($matrixYLength);
        $this->setImgCellOffset($imgCellOffset);
    }

    private function createImageFromPath($imgPath)
    {
        try {
            $this->img = new Imagick();
            $this->img->readImage($imgPath);
        } catch (ImagickException $e) {
            throw new Exception\InvalidImgPathException('It was not possible to read the image from path informed.');
        }
    }

    public function setMatrixYLength($length)
    {
        $this->matrixYLength = $length;
    }

    public function setMatrixXLength($length)
    {
        $this->matrixXLength = $length;
    }

    public function setImgCellOffset($offset)
    {
        $this->imgCellOffset = $offset;
    }

    public function getMarksFromPaths($paths)
    {
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

        // create reference block pattern
        $imageToCompare = new Imagick();
        $imageToCompare->newImage($this->imgCellCompareSizeW, $this->imgCellCompareSizeH, new ImagickPixel('black'));

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

    private function identifyImage()
    {
        return $this->img->identifyImage();
    }

    private function removeColors()
    {
        if ($this->identifyImage()['type'] === 'TrueColor') {
            $this->img->separateImageChannel(Imagick::CHANNEL_RED); // remove red from image
        } else {
            $this->img->modulateImage(100, 0, 100); // desaturate
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
        $imageGeometryInfo = $this->identifyImage()['geometry'];
        $this->imgSizeW = $imageGeometryInfo['width'];
        $this->imgSizeH = $imageGeometryInfo['height'];

        $this->imgCellSizeW = $this->imgSizeW / $this->matrixXLength;
        $this->imgCellSizeH = $this->imgSizeH / $this->matrixYLength;

        $this->imgCellCompareSizeW = $this->imgCellSizeW - ($this->imgCellOffset * 2);
        $this->imgCellCompareSizeH = $this->imgCellSizeH - ($this->imgCellOffset * 2);

        if (self::DEBUG) {
            print_r($this);
        }

    }

    private function getRegionFromImage($row, $col)
    {
        $sizeW = $this->imgCellCompareSizeW;
        $sizeH = $this->imgCellCompareSizeH;
        $pX = ($col * $this->imgCellSizeW) + $this->imgCellOffset;
        $pY = ($row * $this->imgCellSizeW) + $this->imgCellOffset;

        if (self::DEBUG) {
            echo "\n" . $col . " - " . $row . " - " . $pX . " - " . $pY;
        }

        return $this->img->getImageRegion($sizeW, $sizeH, $pX, $pY);
    }

}
