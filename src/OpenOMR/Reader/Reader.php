<?php
namespace OpenOMR\Reader;

use OpenOMR\Exception;
use OpenOMR\PaperSheet\PaperSheet;
use Imagick;
use ImagickPixel;

class Reader
{
    private $paperSheet;
    private $scannedImage;
    private $errorMargin;

    public function __construct($imageFile, PaperSheet $paperSheet, $edgeDislocation = 0, $errorMargin = 0.5)
    {
        if (!extension_loaded('imagick')) {
            throw new Exception\ImagickExtensionNotFoundException('Imagick extension is not loaded.');
        }

        $this->paperSheet = $paperSheet;
        $this->scannedImage = $this->createScannedImage($imageFile, $this->paperSheet->getMatrixLength(), $edgeDislocation);
        $this->errorMargin = $errorMargin;
    }

    public function getResults()
    {
        $imageToCompare = $this->createImageWithBlackBackground($this->scannedImage->getCellWidthForComparison(), $this->scannedImage->getCellHeightForComparison());

        $result = [];

        foreach ($this->paperSheet as $field) {
            if (!isset($result[$field->getIdentifier()])) {
                $result[$field->getIdentifier()] = ['status' => ReadingStatus::INITIAL, 'value' => '', 'error_margin' => 1];
            }

            $fieldStatus = &$result[$field->getIdentifier()]['status'];
            $fieldErrorMargin = &$result[$field->getIdentifier()]['error_margin'];

            //if marked with wrong don't search next path
            if ($fieldStatus === ReadingStatus::FAILURE) {
                continue;
            }

            //if a next char from field reset status
            if ($fieldStatus === ReadingStatus::SUCCESS) {
                $fieldStatus = ReadingStatus::INITIAL;
            }

            foreach ($field as $mark) {
                if ($fieldStatus === ReadingStatus::INITIAL) {
                    $fieldStatus = ReadingStatus::BLANK;
                }

                $regionToCompare = $this->scannedImage->extractRegion($mark->getX(), $mark->getY());

                $differenceBetweenImages = $regionToCompare->compareImages($imageToCompare, Imagick::METRIC_ROOTMEANSQUAREDERROR);
                $metricResult = $differenceBetweenImages[1];

                if ($metricResult < $fieldErrorMargin) {
                    $fieldErrorMargin = $metricResult;
                }

                if ($metricResult <= $this->errorMargin) {
                    //code doesn't exists difference between black square and region marked concatenate the value
                    $result[$field->getIdentifier()]['value'] .= $mark->getValue();

                    $fieldStatus = ($fieldStatus === ReadingStatus::SUCCESS) ? ReadingStatus::FAILURE : ReadingStatus::SUCCESS;
                }

                $regionToCompare->clear();
            }
        }

        $imageToCompare->clear();

        return $result;
    }

    protected function createScannedImage($imageFile, array $matrixLength, $edgeDislocation)
    {
        return new ScannedImage($imageFile, $matrixLength, $edgeDislocation);
    }

    protected function createImageWithBlackBackground($cols, $rows)
    {
        $image = new Imagick();
        $image->newImage($cols, $rows, new ImagickPixel('black'));

        return $image;
    }
}