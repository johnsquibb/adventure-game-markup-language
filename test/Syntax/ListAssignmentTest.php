<?php

namespace AdventureGameMarkupLanguage\Syntax;

use PHPUnit\Framework\TestCase;

class ListAssignmentTest extends TestCase
{
    public function testGetValues()
    {
        $variable = 'tags';
        $values = ['door', 'portal', 'cellar', ['cellar', 'door']];
        $assignment = new ListAssignment($variable, $values);

        $this->assertEquals($values, $assignment->getValues());
    }

    public function testGetVariable()
    {
        $variable = 'tags';
        $values = ['door', 'portal', 'cellar', ['cellar', 'door']];
        $assignment = new ListAssignment($variable, $values);

        $this->assertEquals($variable, $assignment->getVariable());
    }

    public function testToString()
    {
        $variable = 'tags';
        $values = ['door', 'portal', 'cellar', 'cellar door', 'cellar-door'];
        $assignment = new ListAssignment($variable, $values);

        $this->assertEquals(
            'tags=door,portal,cellar,cellar door,cellar-door',
            $assignment->toString()
        );
    }
}
