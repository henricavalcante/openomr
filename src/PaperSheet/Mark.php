<?php
namespace OpenOMR\PaperSheet;

class Mark
{
    private $x;
    private $y;
    private $value;

    public function __construct($x, $y, $value)
    {
        $this->x = (int) $x;
        $this->y = (int) $y;
        $this->value = $value;
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function getValue()
    {
        return $this->value;
    }
}