<?php
namespace OpenOMR;

use OpenOMR\PaperSheet\PaperSheet;
use OpenOMR\PaperSheet\Mark;
use OpenOMR\PaperSheet\Field;
use OpenOMR\Reader\Reader;
use OpenOMR\Reader\ReadingStatus;

class OpenOMRTest extends \PHPUnit_Framework_TestCase
{
    protected $paperSheet;

    protected function setUp()
    {
        $this->paperSheet = new PaperSheet(38, 54);

        for ($i = 29; $i <= 35; $i++) {
            $field = new Field('id');
            $field->addMark(new Mark(19, $i, 1));
            $field->addMark(new Mark(20, $i, 2));
            $field->addMark(new Mark(21, $i, 3));
            $field->addMark(new Mark(22, $i, 4));
            $field->addMark(new Mark(23, $i, 5));
            $field->addMark(new Mark(24, $i, 6));
            $field->addMark(new Mark(25, $i, 7));
            $field->addMark(new Mark(26, $i, 8));
            $field->addMark(new Mark(27, $i, 9));
            $field->addMark(new Mark(28, $i, 0));
            $this->paperSheet->addField($field);
        }

        $fieldId = 1;

        for ($i = 31; $i <= 50; $i++) {
            $field = new Field(str_pad($fieldId, 2, '0', STR_PAD_LEFT));
            $field->addMark(new Mark($i, 3, 'A'));
            $field->addMark(new Mark($i, 4, 'B'));
            $field->addMark(new Mark($i, 5, 'C'));
            $field->addMark(new Mark($i, 6, 'D'));
            $field->addMark(new Mark($i, 7, 'E'));
            $this->paperSheet->addField($field);

            $fieldId++;
        }

        for ($i = 31; $i <= 50; $i++) {
            $field = new Field(str_pad($fieldId, 2, '0', STR_PAD_LEFT));
            $field->addMark(new Mark($i, 10, 'A'));
            $field->addMark(new Mark($i, 11, 'B'));
            $field->addMark(new Mark($i, 12, 'C'));
            $field->addMark(new Mark($i, 13, 'D'));
            $field->addMark(new Mark($i, 14, 'E'));
            $this->paperSheet->addField($field);

            $fieldId++;
        }

        for ($i = 31; $i <= 50; $i++) {
            $field = new Field(str_pad($fieldId, 2, '0', STR_PAD_LEFT));
            $field->addMark(new Mark($i, 17, 'A'));
            $field->addMark(new Mark($i, 18, 'B'));
            $field->addMark(new Mark($i, 19, 'C'));
            $field->addMark(new Mark($i, 20, 'D'));
            $field->addMark(new Mark($i, 21, 'E'));
            $this->paperSheet->addField($field);

            $fieldId++;
        }

        for ($i = 31; $i <= 50; $i++) {
            $field = new Field(str_pad($fieldId, 2, '0', STR_PAD_LEFT));
            $field->addMark(new Mark($i, 24, 'A'));
            $field->addMark(new Mark($i, 25, 'B'));
            $field->addMark(new Mark($i, 26, 'C'));
            $field->addMark(new Mark($i, 27, 'D'));
            $field->addMark(new Mark($i, 28, 'E'));
            $this->paperSheet->addField($field);

            $fieldId++;
        }

        for ($i = 31; $i <= 50; $i++) {
            $field = new Field(str_pad($fieldId, 2, '0', STR_PAD_LEFT));
            $field->addMark(new Mark($i, 31, 'A'));
            $field->addMark(new Mark($i, 32, 'B'));
            $field->addMark(new Mark($i, 33, 'C'));
            $field->addMark(new Mark($i, 34, 'D'));
            $field->addMark(new Mark($i, 35, 'E'));
            $this->paperSheet->addField($field);

            $fieldId++;
        }
    }

    public function testImageWithGoodMarks()
    {
        $omr = new Reader(__DIR__ . '/../_resources/openomr.jpg', $this->paperSheet, 4);
        $result = $omr->getResults();

        $this->assertEquals($result['id']['value'], '132');
        $this->assertEquals('A', $result['01']['value']);
        $this->assertEquals('C', $result['02']['value']);
        $this->assertEquals('C', $result['03']['value']);
        $this->assertEquals('D', $result['04']['value']);
        $this->assertEquals('B', $result['05']['value']);
        $this->assertEquals('C', $result['06']['value']);
        $this->assertEquals('A', $result['07']['value']);
        $this->assertEquals('E', $result['08']['value']);
        $this->assertEquals('E', $result['09']['value']);
        $this->assertEquals('B', $result['10']['value']);
        $this->assertEquals('A', $result['11']['value']);

    }

    public function testImageWithBadMarks()
    {
        $omr = new Reader(__DIR__ . '/../_resources/openomr2.jpg', $this->paperSheet, 4);
        $result = $omr->getResults();

        $this->assertEquals($result['id']['value'], '1234560');
        $this->assertEquals('A', $result['01']['value']);
        $this->assertEquals('C', $result['02']['value']);
        $this->assertEquals('E', $result['03']['value']);
        $this->assertEquals('B', $result['04']['value']);
        $this->assertEquals('D', $result['05']['value']);
        $this->assertEquals('C', $result['06']['value']);
        $this->assertEquals('B', $result['07']['value']);
        $this->assertEquals('D', $result['85']['value']);

        $this->assertEquals(ReadingStatus::SUCCESS, $result['01']['status'], 'check if option 01 is success');
        $this->assertEquals(ReadingStatus::SUCCESS, $result['85']['status'], 'check if option 85 is success');
        $this->assertEquals(ReadingStatus::BLANK, $result['82']['status'], 'check if option 82 is blank');
        $this->assertEquals(ReadingStatus::BLANK, $result['57']['status'], 'check if option 57 is blank');
    }
}