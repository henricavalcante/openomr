<?php
namespace OpenOMR\PaperSheet;

class MarkTest extends \PHPUnit_Framework_TestCase
{
    public function testMarkObjectCreation()
    {
        $mark = new Mark(2, 4, 'foo');

        $this->assertSame(2, $mark->getX());
        $this->assertSame(4, $mark->getY());
        $this->assertEquals('foo', $mark->getValue());
    }
}