<?php
namespace OpenOMR\PaperSheet;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    public function testFieldObjectCreation()
    {
        $field = new Field('id');

        $this->assertEquals('id', $field->getIdentifier());
        $this->assertEmpty($field->getMarks());
    }

    public function testAddMarks()
    {
        $field = new Field('id');
        $field->addMark($this->markObjectMock());
        $field->addMark($this->markObjectMock());
        $field->addMark($this->markObjectMock());

        $this->assertCount(3, $field->getMarks());
    }

    private function markObjectMock()
    {
        return $this->getMockBuilder('OpenOMR\\PaperSheet\\Mark')
            ->setConstructorArgs([2, 4, 'foo']) // these params doesn't matter here..
            ->getMock();
    }
}