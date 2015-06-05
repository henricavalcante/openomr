<?php
namespace OpenOMR;

use Imagick;
use ImagickException;

class ImageImagick extends Imagick
{
    public function readImageFromFilename($filename)
    {
        try {
            $this->readImage($filename);
        } catch (ImagickException $e) {
            throw new Exception\InvalidImagePathException('It was not possible to read the image from path informed.');
        }
    }

    public function adjustImage()
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
        //remove edges and possible skew from image
        $this->trimImage(85);
        $this->deskewImage(15);
        $this->trimImage(85);
        $this->setImagePage(0, 0, 0, 0);
    }
}