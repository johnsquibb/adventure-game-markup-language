<?php

namespace AdventureGameMarkupLanguage\Test;

use AdventureGameMarkupLanguage\Lexer;
use AdventureGameMarkupLanguage\Symbols;
use AdventureGameMarkupLanguage\Syntax\AbstractSyntax;
use AdventureGameMarkupLanguage\Syntax\Assignment;
use AdventureGameMarkupLanguage\Syntax\Identifier;
use AdventureGameMarkupLanguage\Syntax\ListAssignment;
use AdventureGameMarkupLanguage\Syntax\MultilineAssignment;
use AdventureGameMarkupLanguage\Syntax\Type;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    public function testPeekNextToken()
    {
        $lexer = new Lexer();

        $tokens = [];
        $next = $lexer->peekToken(0, $tokens);
        $this->assertEquals(AbstractSyntax::TOKEN_NOTHING, $next);

        $tokens = ['one'];
        $next = $lexer->peekToken(1, $tokens);
        $this->assertEquals(AbstractSyntax::TOKEN_NOTHING, $next);

        $tokens = ['one', 'two'];
        $next = $lexer->peekToken(1, $tokens);
        $this->assertEquals('two', $next);
    }

    public function testScan()
    {
        $lexer = new Lexer();

        $sequence = '';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals([], $lexemes);

        $sequence = '1234567890';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals(['1234567890', AbstractSyntax::TOKEN_NEWLINE], $lexemes);

        $sequence = '12345.67890';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals(['12345.67890', AbstractSyntax::TOKEN_NEWLINE], $lexemes);

        $sequence = 'a b c';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals(
            [
                'a',
                AbstractSyntax::TOKEN_SPACE,
                'b',
                AbstractSyntax::TOKEN_SPACE,
                'c',
                AbstractSyntax::TOKEN_NEWLINE
            ],
            $lexemes
        );

        $sequence = 'a=b';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals(['a', '=', 'b', AbstractSyntax::TOKEN_NEWLINE], $lexemes);

        $sequence = 'a = b';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals(
            [
                'a',
                AbstractSyntax::TOKEN_SPACE,
                '=',
                AbstractSyntax::TOKEN_SPACE,
                'b',
                AbstractSyntax::TOKEN_NEWLINE
            ],
            $lexemes
        );

        $sequence = '[TYPE] a=b c=3';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals(
            [
                '[',
                'TYPE',
                ']',
                AbstractSyntax::TOKEN_SPACE,
                'a',
                '=',
                'b',
                AbstractSyntax::TOKEN_SPACE,
                'c',
                '=',
                '3',
                AbstractSyntax::TOKEN_NEWLINE
            ],
            $lexemes
        );

        $sequence = 'comma, separated, values';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals(
            [
                'comma',
                ',',
                AbstractSyntax::TOKEN_SPACE,
                'separated',
                ',',
                AbstractSyntax::TOKEN_SPACE,
                'values',
                AbstractSyntax::TOKEN_NEWLINE
            ],
            $lexemes
        );

        $sequence = '\[TYPE\]';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals(
            ['\\', '[', 'TYPE', '\\', ']', AbstractSyntax::TOKEN_NEWLINE],
            $lexemes
        );

        $sequence = 'name=\[TYPE\]';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals(
            ['name', '=', '\\', '[', 'TYPE', '\\', ']', AbstractSyntax::TOKEN_NEWLINE],
            $lexemes
        );

        $sequence = 'name=\#string \#with\# pounds\#';
        $lexemes = $lexer->scan($sequence);

        $this->assertEquals(
            [
                'name',
                '=',
                '\\',
                '#',
                'string',
                AbstractSyntax::TOKEN_SPACE,
                '\\',
                '#',
                'with',
                '\\',
                '#',
                AbstractSyntax::TOKEN_SPACE,
                'pounds',
                '\\',
                '#',
                AbstractSyntax::TOKEN_NEWLINE
            ],
            $lexemes
        );
    }

    public function testEvaluateSpaces()
    {
        $lexer = new Lexer();

        // Two spaces surround each word.
        $sequence = '  string  with  extra  space  ';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals(
            [
                'string',
                AbstractSyntax::TOKEN_SPACE,
                AbstractSyntax::TOKEN_SPACE,
                'with',
                AbstractSyntax::TOKEN_SPACE,
                AbstractSyntax::TOKEN_SPACE,
                'extra',
                AbstractSyntax::TOKEN_SPACE,
                AbstractSyntax::TOKEN_SPACE,
                'space',
                AbstractSyntax::TOKEN_NEWLINE
            ],
            $lexemes
        );
    }

    public function testEvaluate()
    {
        $lexer = new Lexer();
        $evaluated = $lexer->evaluate([]);
        $this->assertEquals([], $evaluated);

        $evaluated = $lexer->evaluate(['test', 'abc', '123']);
        $this->assertEquals(
            [
                Symbols::IDENTIFIER,
                'test',
                Symbols::IDENTIFIER,
                'abc',
                Symbols::IDENTIFIER,
                '123',
            ],
            $evaluated
        );

        $evaluated = $lexer->evaluate(['abc', '=', '123']);
        $this->assertEquals(
            [
                Symbols::IDENTIFIER,
                'abc',
                Symbols::EQUALS,
                Symbols::IDENTIFIER,
                '123',
            ],
            $evaluated
        );

        $evaluated = $lexer->evaluate(['[', 'TYPE', ']']);
        $this->assertEquals(
            [
                Symbols::LEFT_BRACKET,
                Symbols::IDENTIFIER,
                'TYPE',
                Symbols::RIGHT_BRACKET,
            ],
            $evaluated
        );

        $evaluated = $lexer->evaluate(['[', 'TYPE', ']']);
        $this->assertEquals(
            [
                Symbols::LEFT_BRACKET,
                Symbols::IDENTIFIER,
                'TYPE',
                Symbols::RIGHT_BRACKET,
            ],
            $evaluated
        );

        $evaluated = $lexer->evaluate(['\\', '[', 'TYPE', '\\', ']']);
        $this->assertEquals(
            [
                Symbols::ESCAPE,
                Symbols::LEFT_BRACKET,
                Symbols::IDENTIFIER,
                'TYPE',
                Symbols::ESCAPE,
                Symbols::RIGHT_BRACKET,
            ],
            $evaluated
        );

        $evaluated = $lexer->evaluate(['\\', '@']);
        $this->assertEquals(
            [
                Symbols::ESCAPE,
                Symbols::IDENTIFIER,
                '@',
            ],
            $evaluated
        );

        $evaluated = $lexer->evaluate(['#', 'comment']);
        $this->assertEquals(
            [
                Symbols::HASH,
                Symbols::IDENTIFIER,
                'comment'
            ],
            $evaluated
        );

        $evaluated = $lexer->evaluate(['comma', ',', 'separated', ',', 'values']);
        $this->assertEquals(
            [
                Symbols::IDENTIFIER,
                'comma',
                Symbols::COMMA,
                Symbols::IDENTIFIER,
                'separated',
                Symbols::COMMA,
                Symbols::IDENTIFIER,
                'values',
            ],
            $evaluated
        );
    }

    public function testTokenize()
    {
        $fixture = <<<END
        [ITEM]
        # Attributes
        id=flashlight
        size =2
        readable = yes
        name = John Smith
        
        # Interactions
        acquirable=yes
        activatable=yes
        deactivatable=no
        
        # Tags 
        tags=flashlight,light,magic torch stick
        END;

        $expected = [
            // [ITEM]
            Symbols::LEFT_BRACKET,
            Symbols::IDENTIFIER,
            'ITEM',
            Symbols::RIGHT_BRACKET,
            Symbols::NEWLINE,

            // id=flashlight
            Symbols::IDENTIFIER,
            'id',
            Symbols::EQUALS,
            Symbols::IDENTIFIER,
            'flashlight',
            Symbols::NEWLINE,

            // size =2
            Symbols::IDENTIFIER,
            'size',
            Symbols::SPACE,
            Symbols::EQUALS,
            Symbols::IDENTIFIER,
            '2',
            Symbols::NEWLINE,

            // readable = yes
            Symbols::IDENTIFIER,
            'readable',
            Symbols::SPACE,
            Symbols::EQUALS,
            Symbols::SPACE,
            Symbols::IDENTIFIER,
            'yes',
            Symbols::NEWLINE,

            // name = John Smith
            Symbols::IDENTIFIER,
            'name',
            Symbols::SPACE,
            Symbols::EQUALS,
            Symbols::SPACE,
            Symbols::IDENTIFIER,
            'John',
            Symbols::SPACE,
            Symbols::IDENTIFIER,
            'Smith',
            Symbols::NEWLINE,

            // Blank line
            Symbols::NEWLINE,

            // acquirable=yes
            Symbols::IDENTIFIER,
            'acquirable',
            Symbols::EQUALS,
            Symbols::IDENTIFIER,
            'yes',
            Symbols::NEWLINE,

            // activatable=yes
            Symbols::IDENTIFIER,
            'activatable',
            Symbols::EQUALS,
            Symbols::IDENTIFIER,
            'yes',
            Symbols::NEWLINE,

            // deactivatable=no
            Symbols::IDENTIFIER,
            'deactivatable',
            Symbols::EQUALS,
            Symbols::IDENTIFIER,
            'no',
            Symbols::NEWLINE,

            // Blank line
            Symbols::NEWLINE,

            // tags=flashlight,light,magic torch stick
            Symbols::IDENTIFIER,
            'tags',
            Symbols::EQUALS,
            Symbols::IDENTIFIER,
            'flashlight',
            Symbols::COMMA,
            Symbols::IDENTIFIER,
            'light',
            Symbols::COMMA,
            Symbols::IDENTIFIER,
            'magic',
            Symbols::SPACE,
            Symbols::IDENTIFIER,
            'torch',
            Symbols::SPACE,
            Symbols::IDENTIFIER,
            'stick',
            Symbols::NEWLINE,
        ];

        $lexer = new Lexer();
        $tokens = $lexer->tokenize($fixture);

        $this->assertEquals($expected, $tokens);
    }

    public function testCreateSyntaxTreeFromIdentifiers()
    {
        $lexer = new Lexer();

        $tokens = [
            Symbols::IDENTIFIER,
            'test',
            Symbols::IDENTIFIER,
            'abc',
            Symbols::IDENTIFIER,
            '123',
        ];

        $tree = $lexer->createSyntaxTree($tokens);
        $nodes = $tree->getNodes();
        $this->assertCount(3, $nodes);
        $this->assertInstanceOf(Identifier::class, $nodes[0]);
        $this->assertInstanceOf(Identifier::class, $nodes[1]);
        $this->assertInstanceOf(Identifier::class, $nodes[2]);
    }

    public function testCreateSyntaxTreeFromEscapes()
    {
        $lexer = new Lexer();

        // Escaped type
        $tokens = [
            Symbols::ESCAPE,
            Symbols::LEFT_BRACKET,
            Symbols::IDENTIFIER,
            'TYPE',
            Symbols::ESCAPE,
            Symbols::RIGHT_BRACKET,
            Symbols::NEWLINE,
        ];

        $tree = $lexer->createSyntaxTree($tokens);
        $nodes = $tree->getNodes();

        $this->assertCount(3, $nodes);

        $this->assertInstanceOf(Identifier::class, $nodes[0]);
        $this->assertEquals('[', $nodes[0]->getValue());

        $this->assertInstanceOf(Identifier::class, $nodes[1]);
        $this->assertEquals('TYPE', $nodes[1]->getValue());

        $this->assertInstanceOf(Identifier::class, $nodes[2]);
        $this->assertEquals(']', $nodes[2]->getValue());

        // Escaped Assignment.
        $tokens = [
            Symbols::IDENTIFIER,
            'a',
            Symbols::ESCAPE,
            Symbols::EQUALS,
            Symbols::IDENTIFIER,
            'b',
        ];

        $tree = $lexer->createSyntaxTree($tokens);
        $nodes = $tree->getNodes();

        $this->assertCount(3, $nodes);

        $this->assertInstanceOf(Identifier::class, $nodes[0]);
        $this->assertEquals('a', $nodes[0]->getValue());

        $this->assertInstanceOf(Identifier::class, $nodes[1]);
        $this->assertEquals('=', $nodes[1]->getValue());

        $this->assertInstanceOf(Identifier::class, $nodes[2]);
        $this->assertEquals('b', $nodes[2]->getValue());

        // Escaped Assignment with multiple identifiers.
        $tokens = [
            Symbols::IDENTIFIER,
            'a',
            Symbols::ESCAPE,
            Symbols::EQUALS,
            Symbols::IDENTIFIER,
            'b',
            Symbols::IDENTIFIER,
            'c',
            Symbols::IDENTIFIER,
            'd',
        ];

        $tree = $lexer->createSyntaxTree($tokens);
        $nodes = $tree->getNodes();

        $this->assertCount(5, $nodes);

        $this->assertInstanceOf(Identifier::class, $nodes[0]);
        $this->assertEquals('a', $nodes[0]->getValue());

        $this->assertInstanceOf(Identifier::class, $nodes[1]);
        $this->assertEquals('=', $nodes[1]->getValue());

        $this->assertInstanceOf(Identifier::class, $nodes[2]);
        $this->assertEquals('b', $nodes[2]->getValue());

        $this->assertInstanceOf(Identifier::class, $nodes[3]);
        $this->assertEquals('c', $nodes[3]->getValue());

        $this->assertInstanceOf(Identifier::class, $nodes[4]);
        $this->assertEquals('d', $nodes[4]->getValue());
    }

    public function testCreateSyntaxTreeFromType()
    {
        $lexer = new Lexer();

        $tokens = [
            Symbols::LEFT_BRACKET,
            Symbols::IDENTIFIER,
            'TYPE',
            Symbols::RIGHT_BRACKET,
        ];

        $tree = $lexer->createSyntaxTree($tokens);
        $nodes = $tree->getNodes();
        $this->assertCount(1, $nodes);
        $this->assertInstanceOf(Type::class, $nodes[0]);
        $this->assertEquals('TYPE', $nodes[0]->getIdentifier());
    }

    public function testCreateSyntaxTreeFromMultilineAssignment()
    {
        $fixture = <<<END
        [description]
        This is a description.
        It spans multiple lines.
        This  line  has  double  spacing.
        END;
        $fixture = preg_replace("/\r\n/", "\n", $fixture);

        $expected = [
            // [description]
            Symbols::LEFT_BRACKET,
            Symbols::IDENTIFIER,
            'description',
            Symbols::RIGHT_BRACKET,
            Symbols::NEWLINE,

            // This is a description.
            Symbols::IDENTIFIER,
            'This',
            Symbols::SPACE,
            Symbols::IDENTIFIER,
            'is',
            Symbols::SPACE,
            Symbols::IDENTIFIER,
            'a',
            Symbols::SPACE,
            Symbols::IDENTIFIER,
            'description.',
            Symbols::NEWLINE,

            // It spans multiple lines.
            Symbols::IDENTIFIER,
            'It',
            Symbols::SPACE,
            Symbols::IDENTIFIER,
            'spans',
            Symbols::SPACE,
            Symbols::IDENTIFIER,
            'multiple',
            Symbols::SPACE,
            Symbols::IDENTIFIER,
            'lines.',
            Symbols::NEWLINE,

            // This  line  has  double  spacing.
            Symbols::IDENTIFIER,
            'This',
            Symbols::SPACE,
            Symbols::SPACE,
            Symbols::IDENTIFIER,
            'line',
            Symbols::SPACE,
            Symbols::SPACE,
            Symbols::IDENTIFIER,
            'has',
            Symbols::SPACE,
            Symbols::SPACE,
            Symbols::IDENTIFIER,
            'double',
            Symbols::SPACE,
            Symbols::SPACE,
            Symbols::IDENTIFIER,
            'spacing.',
            Symbols::NEWLINE,
        ];

        $lexer = new Lexer();
        $tokens = $lexer->tokenize($fixture);

        $this->assertEquals($expected, $tokens);

        $tree = $lexer->createSyntaxTree($tokens);
        $nodes = $tree->getNodes();

        $this->assertCount(1, $nodes);
        $this->assertInstanceOf(MultilineAssignment::class, $nodes[0]);
        $this->assertEquals('description', $nodes[0]->getSection());

        $lines = $nodes[0]->getLines();
        $this->assertCount(3, $lines);
        $this->assertEquals(['This', ' ', 'is', ' ', 'a', ' ', 'description.'], $lines[0]);
        $this->assertEquals(['It', ' ', 'spans', ' ', 'multiple', ' ', 'lines.'], $lines[1]);
        $this->assertEquals(
            [
                'This',
                ' ',
                ' ',
                'line',
                ' ',
                ' ',
                'has',
                ' ',
                ' ',
                'double',
                ' ',
                ' ',
                'spacing.',
            ],
            $lines[2]
        );

        $this->assertEquals($fixture, $nodes[0]->toString());
    }

    public function testCreateSyntaxTreeFromAssignment()
    {
        $lexer = new Lexer();

        $tokens = [
            Symbols::IDENTIFIER,
            'a',
            Symbols::EQUALS,
            Symbols::IDENTIFIER,
            'b',
        ];

        $tree = $lexer->createSyntaxTree($tokens);
        $nodes = $tree->getNodes();
        $this->assertCount(1, $nodes);
        $this->assertInstanceOf(Assignment::class, $nodes[0]);
        $this->assertEquals('a', $nodes[0]->getVariable());
        $this->assertEquals(['b'], $nodes[0]->getValues());

        // Multi-token identifier assignment
        $tokens = [
            Symbols::IDENTIFIER,
            'a',
            Symbols::EQUALS,
            Symbols::IDENTIFIER,
            'The',
            Symbols::IDENTIFIER,
            'Old',
            Symbols::IDENTIFIER,
            'Clock',
        ];

        $tree = $lexer->createSyntaxTree($tokens);
        $nodes = $tree->getNodes();
        $this->assertCount(1, $nodes);
        $this->assertInstanceOf(Assignment::class, $nodes[0]);
        $this->assertEquals('a', $nodes[0]->getVariable());
        $this->assertEquals(['The', 'Old', 'Clock'], $nodes[0]->getValues());
    }

    public function testCreateSyntaxTreeFromListAssignment()
    {
        $lexer = new Lexer();

        $tokens = [
            Symbols::IDENTIFIER,
            'a',
            Symbols::EQUALS,
            Symbols::IDENTIFIER,
            'b',
            Symbols::COMMA,
            Symbols::IDENTIFIER,
            'c',
            Symbols::COMMA,
            Symbols::IDENTIFIER,
            'd',
        ];

        $tree = $lexer->createSyntaxTree($tokens);
        $nodes = $tree->getNodes();

        $this->assertCount(1, $nodes);
        $this->assertInstanceOf(ListAssignment::class, $nodes[0]);
        $this->assertEquals('a', $nodes[0]->getVariable());
        $this->assertEquals(['b', 'c', 'd'], $nodes[0]->getValues());

        // Multi-token identifier CSV
        $tokens = [
            Symbols::IDENTIFIER,
            'a',
            Symbols::EQUALS,
            Symbols::IDENTIFIER,
            'b',
            Symbols::IDENTIFIER,
            'as',
            Symbols::IDENTIFIER,
            'in',
            Symbols::IDENTIFIER,
            'boy',
            Symbols::COMMA,
            Symbols::IDENTIFIER,
            'c',
            Symbols::IDENTIFIER,
            'as',
            Symbols::IDENTIFIER,
            'in',
            Symbols::IDENTIFIER,
            'cat',
            Symbols::COMMA,
            Symbols::IDENTIFIER,
            'd',
            Symbols::IDENTIFIER,
            'as',
            Symbols::IDENTIFIER,
            'in',
            Symbols::IDENTIFIER,
            'dog',
        ];

        $tree = $lexer->createSyntaxTree($tokens);
        $nodes = $tree->getNodes();

        $this->assertCount(1, $nodes);
        $this->assertInstanceOf(ListAssignment::class, $nodes[0]);
        $this->assertEquals('a', $nodes[0]->getVariable());
        $this->assertEquals(
            [
                [
                    'b',
                    'as',
                    'in',
                    'boy'
                ],

                [
                    'c',
                    'as',
                    'in',
                    'cat'
                ],

                [
                    'd',
                    'as',
                    'in',
                    'dog'
                ]
            ],
            $nodes[0]->getValues()
        );
    }

    public function testCreateSyntaxTreeFromMultipleTypes()
    {
        $lexer = new Lexer();

        $tokens = [
            // [ITEM]
            Symbols::LEFT_BRACKET,
            Symbols::IDENTIFIER,
            'ITEM',
            Symbols::RIGHT_BRACKET,

            // name=The Old Clock
            Symbols::IDENTIFIER,
            'name',
            Symbols::EQUALS,
            Symbols::IDENTIFIER,
            'The',
            Symbols::IDENTIFIER,
            'Old',
            Symbols::IDENTIFIER,
            'Clock',

            // activated=yes
            Symbols::IDENTIFIER,
            'activated',
            Symbols::EQUALS,
            Symbols::IDENTIFIER,
            'yes',

            // acquirable=no
            Symbols::IDENTIFIER,
            'acquirable',
            Symbols::EQUALS,
            Symbols::IDENTIFIER,
            'no',

            // tags=key,key to door,key.to.door
            Symbols::IDENTIFIER,
            'tags',
            Symbols::EQUALS,
            Symbols::IDENTIFIER,
            'key',
            Symbols::COMMA,
            Symbols::IDENTIFIER,
            'key',
            Symbols::IDENTIFIER,
            'to',
            Symbols::IDENTIFIER,
            'door',
            Symbols::COMMA,
            Symbols::IDENTIFIER,
            'key.to.door',
        ];

        $tree = $lexer->createSyntaxTree($tokens);
        $nodes = $tree->getNodes();

        // [ITEM]
        $this->assertInstanceOf(Type::class, $nodes[0]);
        $this->assertEquals('ITEM', $nodes[0]->getIdentifier());

        // name=The Old Clock

        $this->assertInstanceOf(Assignment::class, $nodes[1]);
        $this->assertEquals('name', $nodes[1]->getVariable());
        $this->assertEquals(['The', 'Old', 'Clock'], $nodes[1]->getValues());

        // activated=yes
        $this->assertInstanceOf(Assignment::class, $nodes[2]);
        $this->assertEquals('activated', $nodes[2]->getVariable());
        $this->assertEquals(['yes'], $nodes[2]->getValues());

        // acquirable=no
        $this->assertInstanceOf(Assignment::class, $nodes[3]);
        $this->assertEquals('acquirable', $nodes[3]->getVariable());
        $this->assertEquals(['no'], $nodes[3]->getValues());

        // tags=key,key to door,key.to.door
        $this->assertInstanceOf(ListAssignment::class, $nodes[4]);
        $this->assertEquals('tags', $nodes[4]->getVariable());

        $this->assertEquals(
            ['key', ['key', 'to', 'door'], 'key.to.door'],
            $nodes[4]->getValues()
        );
    }
}
