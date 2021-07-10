<?php

namespace AdventureGameMarkupLanguage\Test;

use AdventureGameMarkupLanguage\Hydrator\ContainerEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\EventEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\ItemEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\LocationEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\TriggerEntityHydrator;
use AdventureGameMarkupLanguage\Lexer;
use AdventureGameMarkupLanguage\Parser;
use AdventureGameMarkupLanguage\Transpiler;
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
        Serial Number: #8301IDI001256703\\\B
        Batt. Type: (4) \[AA\]\,    Rechargeable\=yes
        
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
        
        [LOCATION]
        id=theVastExpanse
        name=The Vast Expanse
        [description]
        A seemingly ever-sprawling expanse of nothingness.
        It goes on for miles and miles, well beyond your line of sight.
        
        [CONTAINER]
        id=theTreasureChest
        name=A Treasure Chest
        locked=yes
        key=keyToTreasureChest
        [description]
        A chest containing valuable items.
        It appears to be locked.
        
        [TRIGGER]
        id=theTriggerId
        type=theTriggerType
        uses=123
        activators=one,two,three
        comparisons=four,five,six
        item=theItemId
        location=theLocationId
        portal=thePortalId
        
        [EVENT]
        id=theEventId
        type=theEventType
        trigger=theEventTrigger
        END;

        $lexer = new Lexer();
        $parser = new Parser();
        $transpiler = new Transpiler($lexer, $parser);

        $hydrators = $transpiler->transpile($fixture);

        $this->assertCount(6, $hydrators);

        $this->assertInstanceOf(ItemEntityHydrator::class, $hydrators[0]);
        $this->assertEquals('flashlight', $hydrators[0]->getId());
        $this->assertCount(6, $hydrators[0]->getText());
        $this->assertEquals(
            "Serial Number: #8301IDI001256703\\B",
            $hydrators[0]->getText()[3]
        );
        $this->assertEquals(
            "Batt. Type: (4) [AA],    Rechargeable=yes",
            $hydrators[0]->getText()[4]
        );

        $this->assertInstanceOf(ItemEntityHydrator::class, $hydrators[1]);
        $this->assertEquals('keyToWoodenDoor', $hydrators[1]->getId());

        $this->assertInstanceOf(LocationEntityHydrator::class, $hydrators[2]);
        $this->assertEquals('theVastExpanse', $hydrators[2]->getId());

        $this->assertInstanceOf(ContainerEntityHydrator::class, $hydrators[3]);
        $this->assertEquals('theTreasureChest', $hydrators[3]->getId());

        $this->assertInstanceOf(TriggerEntityHydrator::class, $hydrators[4]);
        $this->assertEquals('theTriggerId', $hydrators[4]->getId());
        $this->assertEquals('theTriggerType', $hydrators[4]->getType());
        $this->assertEquals(123, $hydrators[4]->getUses());
        $this->assertEquals(['one', 'two', 'three'], $hydrators[4]->getActivators());
        $this->assertEquals(['four', 'five', 'six'], $hydrators[4]->getComparisons());
        $this->assertEquals('theItemId', $hydrators[4]->getItem());
        $this->assertEquals('theLocationId', $hydrators[4]->getLocation());
        $this->assertEquals('thePortalId', $hydrators[4]->getPortal());

        $this->assertInstanceOf(EventEntityHydrator::class, $hydrators[5]);
        $this->assertEquals('theEventId', $hydrators[5]->getId());
        $this->assertEquals('theEventType', $hydrators[5]->getType());
        $this->assertEquals('theEventTrigger', $hydrators[5]->getTrigger());
    }
}
