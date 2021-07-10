<?php

namespace AdventureGameMarkupLanguage\Test\Syntax;

use AdventureGameMarkupLanguage\Syntax\Assignment;
use PHPUnit\Framework\TestCase;

class AssignmentTest extends TestCase
{
    public function testGetValues()
    {
        $variable = 'name';
        $values = ['John', 'the', 'Carpenter'];
        $assignment = new Assignment($variable, $values);

        $this->assertEquals($values, $assignment->getValues());
    }

    public function testGetVariable()
    {
        $variable = 'name';
        $values = ['John', 'the', 'Carpenter'];
        $assignment = new Assignment($variable, $values);

        $this->assertEquals($variable, $assignment->getVariable());
    }

    public function testToString()
    {
        $variable = 'name';
        $values = ['John', ' ', 'the', ' ', 'Carpenter'];
        $assignment = new Assignment($variable, $values);

        $this->assertEquals('name=John the Carpenter', $assignment->toString());
    }
}
