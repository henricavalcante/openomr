<?php
namespace OpenOMR;

use OpenOMR\Exception;
use Imagick;
use ImagickPixel;

class Reader
{
    private $imgCellSizeW;
    private $imgCellSizeH;
    private $imgCellCompareSizeW;
    private $imgCellCompareSizeH;

    private $paperSheet;
    private $image;
    private $errorMargin = 0.5;

    public function __construct(PaperSheetInterface $paperSheet)
    {
        if (!extension_loaded('imagick')) {
            throw new Exception\ImagickExtensionNotFoundException('Imagick extension is not loaded.');
        }

        $this->paperSheet = $paperSheet;
        $this->image = new ImageImagick();
        $this->image->readImageFromFilename($this->paperSheet->getSource());
    }

    public function getResults()
    {
        $this->image->adjustImage();
        $this->calculateSizes();

        // create reference block pattern
        $imageToCompare = new Imagick();
        $imageToCompare->newImage($this->imgCellCompareSizeW, $this->imgCellCompareSizeH, new ImagickPixel('black'));

        $result = [];

        foreach ($this->paperSheet->getFields() as $field) {

            if (!isset($result[$field->getIdentifier()])) {
                $result[$field->getIdentifier()] = ['status' => 0, 'value' => '', 'error_margin' => 1];
            }

            //if marked with wrong dont search next path
            if ($result[$field->getIdentifier()]['status'] === 3) {
                continue;
            }

            //if a next char from field reset status
            if ($result[$field->getIdentifier()]['status'] === 2) {
                $result[$field->getIdentifier()]['status'] = 0;
            }

            foreach ($field->getMarks() as $mark) {
                if ($result[$field->getIdentifier()]['status'] === 0) {
                    $result[$field->getIdentifier()]['status'] = 1;
                }

                $regionToCompare = $this->getRegionFromImage($mark->getX(), $mark->getY());

                $differenceBetweenImages = $regionToCompare->compareImages($imageToCompare,
                    Imagick::METRIC_ROOTMEANSQUAREDERROR);

                if ($differenceBetweenImages[1] < $result[$field->getIdentifier()]['error_margin']) {
                    $result[$field->getIdentifier()]['error_margin'] = $differenceBetweenImages[1];
                }

                if ($differenceBetweenImages[1] < $this->errorMargin) {
                    //cade doesn't exists difference between black square and region marked concatenate the value
                    $result[$field->getIdentifier()]['value'] .= $mark->getValue();

                    if ($result[$field->getIdentifier()]['status'] === 2) {
                        $result[$field->getIdentifier()]['status'] = 3;
                    } else {
                        $result[$field->getIdentifier()]['status'] = 2;
                    }
                }

                $regionToCompare->clear();
            }
        }

        $imageToCompare->clear();

        return $result;
    }

    private function calculateSizes()
    {
        $imageGeometryInfo = $this->image->identifyImage()['geometry'];

        $this->imgCellSizeW = $imageGeometryInfo['width'] / $this->paperSheet->getMatrixLength()['x'];
        $this->imgCellSizeH = $imageGeometryInfo['height'] / $this->paperSheet->getMatrixLength()['y'];

        $this->imgCellCompareSizeW = $this->imgCellSizeW - (4 * 2);
        $this->imgCellCompareSizeH = $this->imgCellSizeH - (4 * 2);
    }

    private function getRegionFromImage($row, $col)
    {
        $sizeW = $this->imgCellCompareSizeW;
        $sizeH = $this->imgCellCompareSizeH;
        $pX = ($col * $this->imgCellSizeW) + 4;
        $pY = ($row * $this->imgCellSizeW) + 4;

        $image = $this->image->getImageRegion($sizeW, $sizeH, $pX, $pY);
        $image->setImagePage(0, 0, 0, 0);

        return $image;
    }

}
