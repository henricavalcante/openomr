<?php
namespace OpenOMR;

use OpenOMR\Mark;

class Field
{
    private $identifier;
    private $marks;

    public function __construct($identifier)
    {
        $this->identifier = $identifier;
        $this->marks = [];
    }

    public function setMark(Mark $mark)
    {
        $this->marks[] = [$mark->getX(), $mark->getY(), $mark->getValue()];
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getMarks()
    {
        return $this->marks;
    }
}