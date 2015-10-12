<?php
namespace OpenOMR\PaperSheet;

use IteratorAggregate;
use ArrayIterator;

class PaperSheet implements IteratorAggregate
{
    private $matrixLength = [];
    private $fields = [];

    public function __construct($x, $y)
    {
        $this->matrixLength = [(int) $x, (int) $y];
    }

    public function addField(Field $field)
    {
        $this->fields[] = $field;
    }

    public function getMatrixLength()
    {
        return $this->matrixLength;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->fields);
    }
}