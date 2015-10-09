<?php
namespace OpenOMR;

use Imagick;

class ScannedImage extends Imagick
{
    private $matrixLength;

    private $cellWidth;
    private $cellHeight;
    private $cellWidthForComparison;
    private $cellHeightForComparison;

    private $edgeDislocation;

    public function __construct($files, array $matrixLength, $edgeDislocation)
    {
        parent::__construct(realpath($files));

        $this->adaptImageForReading();

        $this->edgeDislocation = $edgeDislocation;
        $this->matrixLength = $matrixLength;
    }

    public function getCellWidth()
    {
        if (empty($this->cellWidth)) {
            $this->cellWidth = $this->getGeometryInfo()['width'] / $this->matrixLength[0];
        }

        return $this->cellWidth;
    }

    public function getCellWidthForComparison()
    {
        if (empty($this->cellWidthForComparison)) {
            $this->cellWidthForComparison = $this->getCellWidth() - ($this->edgeDislocation * 2);
        }

        return $this->cellWidthForComparison;
    }

    public function getCellHeight()
    {
        if (empty($this->cellHeight)) {
            $this->cellHeight = $this->getGeometryInfo()['height'] / $this->matrixLength[1];
        }

        return $this->cellHeight;
    }

    public function getCellHeightForComparison()
    {
        if (empty($this->cellHeightForComparison)) {
            $this->cellHeightForComparison = $this->getCellHeight() - ($this->edgeDislocation * 2);
        }

        return $this->cellHeightForComparison;
    }

    public function extractRegion($row, $col)
    {
        $width = $this->getCellWidthForComparison();
        $height = $this->getCellHeightForComparison();
        $x = ($col * $this->getCellWidth()) + $this->edgeDislocation;
        $y = ($row * $this->getCellHeight()) + $this->edgeDislocation;

        $image = $this->getImageRegion($width, $height, $x, $y);
        $image->setImagePage(0, 0, 0, 0);

        return $image;
    }

    protected function getGeometryInfo()
    {
        return $this->identifyImage()['geometry'];
    }

    protected function adaptImageForReading()
    {
        $this->enhanceImageQuality();
        $this->turnImageIntoBlackAndWhite();
        $this->removeEdges();
    }

    protected function enhanceImageQuality()
    {
        $this->normalizeImage();
        $this->enhanceImage();
        $this->despeckleImage();
    }

    protected function turnImageIntoBlackAndWhite()
    {
        $this->blackThresholdImage('#808080');
        $this->whiteThresholdImage('#808080');
    }

    protected function removeEdges()
    {
        $this->trimImage(85);
        $this->deskewImage(15);
        $this->trimImage(85);
        $this->setImagePage(0, 0, 0, 0);
    }
}