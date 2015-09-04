<?php
namespace OpenOMR;

use OpenOMR\PaperSheetInterface;
use OpenOMR\Field;

class PaperSheet implements PaperSheetInterface
{
    private $source;
    private $matrixLength = [];
    private $fields = [];

    public function __construct($source, array $matrixLength = null)
    {
        $this->source = $source;

        if ($matrixLength) {
            $this->setMatrixLength($matrixLength[0], $matrixLength[1]);
        }
    }

    public function setMatrixLength($x, $y)
    {
        $this->matrixLength = ['x' => (int) $x, 'y' => (int) $y];
    }

    public function setField(Field $field)
    {
        $this->fields[] = $field;
    }

    public function getSource()
    {
        return $this->source;
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