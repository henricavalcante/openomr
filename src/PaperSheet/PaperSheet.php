<?php
namespace OpenOMR\PaperSheet;

class PaperSheet
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
}