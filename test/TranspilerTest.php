<?php

namespace AdventureGameMarkupLanguage;

use AdventureGameMarkupLanguage\Hydrator\ItemEntityHydrator;
use PHPUnit\Framework\TestCase;

class TranspilerTest extends TestCase
{
    public function testTranspile()
    {
        $fixture = <<<END
        [ITEM]
        # Attributes
        id=flashlight
        size =2
        readable = yes
        name = Small Flashlight
        
        # Interactions
        acquirable=yes
        activatable=yes
        deactivatable=no
        
        # Tags 
        tags=flashlight,light,magic torch stick
        
        [description]
        A black metal flashlight that runs on rechargeable batteries.
        There is a round gray button for activating it.
        There is some small text printed on a label on the side of the flashlight.
        
        [text]
        Information written on the side:
        Model: Illuminated Devices Inc
        Year: 1983
        Serial Number: 8301IDI001256703
        Batt. Type: (4) AA
        
        [ITEM]
        # Attributes
        id=keyToWoodenDoor
        size=1
        name = Key to the Wooden Door
        
        # Interactions
        acquirable=yes
        
        # Tags 
        tags=key,key to wooden door
        
        [description]
        A small brass key that goes to a heavy wooden door.
        END;

        $lexer = new Lexer();
        $parser = new Parser();
        $transpiler = new Transpiler($lexer, $parser);

        $hydrators = $transpiler->transpile($fixture);

        $this->assertCount(2, $hydrators);
        $this->assertInstanceOf(ItemEntityHydrator::class, $hydrators[0]);
        $this->assertEquals('flashlight', $hydrators[0]->getId());
        $this->assertInstanceOf(ItemEntityHydrator::class, $hydrators[1]);
        $this->assertEquals('keyToWoodenDoor', $hydrators[1]->getId());
    }
}