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
            return $this;
        } catch (ImagickException $e) {
            throw new Exception\InvalidImgPathException('It was not possible to read the image from path informed.');
        }
    }

    public function adjustImage()
    {
        $this->normalizeImage();
        $this->enhanceImage();
        $this->despeckleImage();
        $this->blackThresholdImage('#808080');
        $this->whiteThresholdImage('#808080');
    }

    public function removeEdges()
    {
        //remove edges and possible skew from image
        $this->trimImage(85);
        $this->deskewImage(15);
        $this->trimImage(85);
        $this->setImagePage(0, 0, 0, 0);
    }
}