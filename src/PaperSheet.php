<?php
namespace OpenOMR;

class PaperSheet implements PaperSheetInterface
{
    private $matrixLength = [];
    private $fields = [];

    public function __construct(array $matrixLength)
    {
        $this->setMatrixLength($matrixLength[0], $matrixLength[1]);
    }

    public function setMatrixLength($x, $y)
    {
        $this->matrixLength = ['x' => (int) $x, 'y' => (int) $y];
    }

    public function setField(Field $field)
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