<?php

namespace AdventureGameMarkupLanguage\Test;

use AdventureGameMarkupLanguage\Exception\InvalidTypeException;
use AdventureGameMarkupLanguage\Hydrator\ContainerEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\ItemEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\LocationEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\PortalEntityHydrator;
use AdventureGameMarkupLanguage\Parser;
use AdventureGameMarkupLanguage\Syntax\Assignment;
use AdventureGameMarkupLanguage\Syntax\ListAssignment;
use AdventureGameMarkupLanguage\Syntax\MultilineAssignment;
use AdventureGameMarkupLanguage\Syntax\Type;
use AdventureGameMarkupLanguage\SyntaxTree;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testParseTreeContainsItem()
    {
        $item = new Type('ITEM');
        $name = new Assignment('name', ['A', ' ', 'pack', ' ', 'of', ' ', 'gum']);
        $id = new Assignment('id', ['thePackOfGum']);
        $size = new Assignment('size', ['123']);
        $acquirable = new Assignment('acquirable', ['yes']);
        $activatable = new Assignment('activatable', ['no']);
        $deactivatable = new Assignment('deactivatable', ['no']);
        $tags = new ListAssignment('tags', ['gum', 'pack of gum', 'the pack of gum']);
        $phrases = new ListAssignment('phrases', ['chew', 'bubble gum']);
        $description = new MultilineAssignment(
            'description',
            [
            ['This', ' ', 'is', ' ', 'the', ' ', 'description.'],
            ['It', ' ', 'spans', ' ', 'multiple', ' ', 'lines.'],
        ]
        );
        $text = new MultilineAssignment(
            'text',
            [
            ['This', ' ', 'is', ' ', 'readable', ' ', 'text.'],
            ['It', ' ', 'spans', ' ', 'multiple', ' ', 'lines.'],
        ]
        );

        $syntaxTree = new SyntaxTree();
        $syntaxTree->addNode($item);
        $syntaxTree->addNode($name);
        $syntaxTree->addNode($id);
        $syntaxTree->addNode($size);
        $syntaxTree->addNode($acquirable);
        $syntaxTree->addNode($activatable);
        $syntaxTree->addNode($deactivatable);
        $syntaxTree->addNode($tags);
        $syntaxTree->addNode($phrases);
        $syntaxTree->addNode($description);
        $syntaxTree->addNode($text);

        $parser = new Parser();
        $hydrators = $parser->parseTree($syntaxTree);

        $this->assertCount(1, $hydrators);
        $this->assertInstanceOf(ItemEntityHydrator::class, $hydrators[0]);
        $this->assertEquals($hydrators[0]->getName(), 'A pack of gum');
        $this->assertEquals($hydrators[0]->getId(), 'thePackOfGum');
        $this->assertEquals($hydrators[0]->getSize(), 123);
        $this->assertEquals($hydrators[0]->getAcquirable(), true);
        $this->assertEquals($hydrators[0]->getActivatable(), false);
        $this->assertEquals($hydrators[0]->getDeactivatable(), false);
        $this->assertEquals($hydrators[0]->getTags(), ['gum', 'pack of gum', 'the pack of gum']);
        $this->assertEquals($hydrators[0]->getPhrases(), ['chew', 'bubble gum']);
        $this->assertEquals(
            $hydrators[0]->getDescription(),
            [
                'This is the description.',
                'It spans multiple lines.'
            ]
        );
        $this->assertEquals(
            $hydrators[0]->getText(),
            [
                'This is readable text.',
                'It spans multiple lines.'
            ]
        );
    }

    public function testParseTreeContainsPortal()
    {
        $portal = new Type('PORTAL');
        $name = new Assignment('name', ['A', ' ', 'door', ' ', 'to', ' ', 'nowhere']);
        $locked = new Assignment('locked', ['no']);

        $syntaxTree = new SyntaxTree();
        $syntaxTree->addNode($portal);
        $syntaxTree->addNode($name);
        $syntaxTree->addNode($locked);

        $parser = new Parser();
        $hydrators = $parser->parseTree($syntaxTree);

        $this->assertCount(1, $hydrators);
        $this->assertInstanceOf(PortalEntityHydrator::class, $hydrators[0]);
        $this->assertEquals($hydrators[0]->getName(), 'A door to nowhere');
        $this->assertEquals($hydrators[0]->getLocked(), false);
    }

    public function testParseTreeContainsLocation()
    {
        $location = new Type('LOCATION');
        $name = new Assignment('name', ['The', ' ', 'Vast', ' ', 'Expanse']);
        $items = new ListAssignment('items', ['treasureChest', 'keyToTreasureChest']);

        $syntaxTree = new SyntaxTree();
        $syntaxTree->addNode($location);
        $syntaxTree->addNode($name);
        $syntaxTree->addNode($items);

        $parser = new Parser();
        $hydrators = $parser->parseTree($syntaxTree);

        $this->assertCount(1, $hydrators);
        $this->assertInstanceOf(LocationEntityHydrator::class, $hydrators[0]);
        $this->assertEquals($hydrators[0]->getName(), 'The Vast Expanse');
        $this->assertEquals($hydrators[0]->getItems(), ['treasureChest', 'keyToTreasureChest']);
    }

    public function testParseTreeContainsContainer()
    {
        $container = new Type('CONTAINER');
        $name = new Assignment('name', ['A', ' ', 'Treasure', ' ', 'Chest']);
        $items = new ListAssignment('items', ['flashlight', 'keyToWoodenDoor']);

        $syntaxTree = new SyntaxTree();
        $syntaxTree->addNode($container);
        $syntaxTree->addNode($name);
        $syntaxTree->addNode($items);

        $parser = new Parser();
        $hydrators = $parser->parseTree($syntaxTree);

        $this->assertCount(1, $hydrators);
        $this->assertInstanceOf(ContainerEntityHydrator::class, $hydrators[0]);
        $this->assertEquals($hydrators[0]->getName(), 'A Treasure Chest');
        $this->assertEquals($hydrators[0]->getItems(), ['flashlight', 'keyToWoodenDoor']);
    }

    public function testParseTreeContainsInvalidType()
    {
        $thing = new Type('THING');
        $syntaxTree = new SyntaxTree();
        $syntaxTree->addNode($thing);

        $parser = new Parser();

        $this->expectException(InvalidTypeException::class);
        $parser->parseTree($syntaxTree);
    }
}
