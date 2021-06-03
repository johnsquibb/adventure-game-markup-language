<?php

namespace AdventureGameMarkupLanguage\Syntax;

use PHPUnit\Framework\TestCase;

class IdentifierTest extends TestCase
{
    public function testToString()
    {
        $value = 'value';
        $identifier = new Identifier($value);
        $this->assertEquals($value, $identifier->toString());
    }

    public function testGetValue()
    {
        $value = 'value';
        $identifier = new Identifier($value);
        $this->assertEquals($value, $identifier->getValue());
    }
}
