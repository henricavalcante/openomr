<?php
namespace OpenOMR;

interface PaperSheetInterface
{
    public function __construct(array $matrixLength);
    public function setMatrixLength($x, $y);
    public function setField(Field $field);
    public function getMatrixLength();
    public function getFields();
}