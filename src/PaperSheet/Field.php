<?php
namespace OpenOMR\PaperSheet;

class Field
{
    private $identifier;
    private $marks = [];

    public function __construct($identifier)
    {
        $this->identifier = $identifier;
    }

    public function addMark(Mark $mark)
    {
        $this->marks[] = $mark;
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