<?php
namespace OpenOMR\PaperSheet;

class PaperSheetTest extends \PHPUnit_Framework_TestCase
{
    public function testPaperSheetObjectCreation()
    {
        $sheet = new PaperSheet(40, 60);

        $this->assertEmpty($sheet->getFields());
        $this->assertEquals(40, $sheet->getMatrixLength()[0]);
        $this->assertEquals(60, $sheet->getMatrixLength()[1]);
    }

    public function testAddFields()
    {
        $sheet = new PaperSheet(40, 60);

        $sheet->addField($this->fieldObjectMock());
        $sheet->addField($this->fieldObjectMock());
        $sheet->addField($this->fieldObjectMock());

        $this->assertCount(3, $sheet->getFields());
    }

    private function fieldObjectMock()
    {
        return $this->getMockBuilder('OpenOMR\\PaperSheet\\Field')
            ->setConstructorArgs(['foo']) // this param doesn't matter here..
            ->getMock();
    }
}