<?php

namespace AdventureGameMarkupLanguage\Test\Syntax;

use AdventureGameMarkupLanguage\Syntax\Type;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    public function testToString()
    {
        $type = new Type('LOCATION');

        $this->assertEquals('[LOCATION]', $type->toString());
    }

    public function testGetType()
    {
        $identifier = 'ITEM';
        $type = new Type($identifier);
        $this->assertEquals($identifier, $type->getIdentifier());
    }
}
