<?php

namespace AdventureGameMarkupLanguage\Syntax;

use PHPUnit\Framework\TestCase;

class MultilineAssignmentTest extends TestCase
{
    public function testGetValues()
    {
        $values = ['John', 'the', 'Carpenter'];
        $assignment = new MultilineAssignment('', $values);

        $this->assertEquals($values, $assignment->getLines());
    }

    public function testGetSection()
    {
        $section = 'description';
        $assignment = new MultilineAssignment($section, []);

        $this->assertEquals($section, $assignment->getSection());
    }

    public function testToString()
    {
        $section = 'description';
        $lines = [
            ['The', 'flashlight', 'is', 'black'],
            ['It', 'is', 'made', 'of', 'metal'],
        ];
        $assignment = new MultilineAssignment($section, $lines);

        $expected = <<<END
        [description]
        The flashlight is black
        It is made of metal
        END;
        $expected = preg_replace("/\r\n/", "\n", $expected);

        $this->assertEquals(trim($expected), $assignment->toString());
    }
}
