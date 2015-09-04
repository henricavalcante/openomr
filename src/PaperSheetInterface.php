<?php
namespace OpenOMR;

use OpenOMR\Field;

interface PaperSheetInterface
{
    public function __construct($source, array $matrixLength = null);
    public function setMatrixLength($x, $y);
    public function setField(Field $field);
    public function getSource();
    public function getMatrixLength();
    public function getFields();
}