<?php

namespace AdventureGameMarkupLanguage;

use AdventureGameMarkupLanguage\Hydrator\ItemHydrator;
use AdventureGameMarkupLanguage\Syntax\Assignment;
use AdventureGameMarkupLanguage\Syntax\ListAssignment;
use AdventureGameMarkupLanguage\Syntax\MultilineAssignment;
use AdventureGameMarkupLanguage\Syntax\Type;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testParseTree()
    {
        $item = new Type('ITEM');
        $name = new Assignment('name', ['A', 'pack', 'of', 'gum']);
        $id = new Assignment('id', ['thePackOfGum']);
        $size = new Assignment('size', ['123']);
        $acquirable = new Assignment('acquirable', ['yes']);
        $activatable = new Assignment('activatable', ['no']);
        $deactivatable = new Assignment('deactivatable', ['no']);
        $tags = new ListAssignment('tags', ['gum', 'pack of gum', 'the pack of gum']);
        $phrases = new ListAssignment('phrases', ['chew', 'bubble gum']);
        $description = new MultilineAssignment('description', [
            ['This', 'is', 'the', 'description.'],
            ['It', 'spans', 'multiple', 'lines.'],
        ]);
        $text = new MultilineAssignment('text', [
            ['This', 'is', 'readable', 'text.'],
            ['It', 'spans', 'multiple', 'lines.'],
        ]);

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
        $this->assertInstanceOf(ItemHydrator::class, $hydrators[0]);
        $this->assertEquals($hydrators[0]->getName(), 'A pack of gum');
        $this->assertEquals($hydrators[0]->getId(), 'thePackOfGum');
        $this->assertEquals($hydrators[0]->getSize(), 123);
        $this->assertEquals($hydrators[0]->getAcquirable(), true);
        $this->assertEquals($hydrators[0]->getActivatable(), false);
        $this->assertEquals($hydrators[0]->getDeactivatable(), false);
        $this->assertEquals($hydrators[0]->getTags(), ['gum', 'pack of gum', 'the pack of gum']);
        $this->assertEquals($hydrators[0]->getPhrases(), ['chew', 'bubble gum']);
        $this->assertEquals($hydrators[0]->getDescription(), [
            'This is the description.',
            'It spans multiple lines.'
        ]);
        $this->assertEquals($hydrators[0]->getText(), [
            'This is readable text.',
            'It spans multiple lines.'
        ]);
    }
}
