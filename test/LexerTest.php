<?php

namespace AdventureGameMarkupLanguage;

use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    public function testScan()
    {
        $lexer = new Lexer();

        $sequence = '';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals([], $lexemes);

        $sequence = '1234567890';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals(['1234567890'], $lexemes);

        $sequence = '12345.67890';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals(['12345.67890'], $lexemes);

        $sequence = 'a b c';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals(['a', 'b', 'c'], $lexemes);

        $sequence = 'a=b';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals(['a', '=', 'b'], $lexemes);

        $sequence = 'a = b';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals(['a', '=', 'b'], $lexemes);

        $sequence = '[TYPE] a=b c=3';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals(['[', 'TYPE', ']', 'a', '=', 'b', 'c', '=', '3'], $lexemes);

        $sequence = '# comment format';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals(['#', 'comment', 'format'], $lexemes);

        $sequence = 'comma, separated, values';
        $lexemes = $lexer->scan($sequence);
        $this->assertEquals(['comma', ',', 'separated', ',', 'values'], $lexemes);
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
                Symbols::IDENTIFIER,
                Symbols::IDENTIFIER,
            ],
            $evaluated
        );

        $evaluated = $lexer->evaluate(['abc', '=', '123']);
        $this->assertEquals(
            [
                Symbols::IDENTIFIER,
                Symbols::EQUALS,
                Symbols::IDENTIFIER,
            ],
            $evaluated
        );

        $evaluated = $lexer->evaluate(['[', 'TYPE', ']']);
        $this->assertEquals(
            [
                Symbols::LEFT_BRACKET,
                Symbols::IDENTIFIER,
                Symbols::RIGHT_BRACKET,
            ],
            $evaluated
        );

        $evaluated = $lexer->evaluate(['[', 'TYPE', ']']);
        $this->assertEquals(
            [
                Symbols::LEFT_BRACKET,
                Symbols::IDENTIFIER,
                Symbols::RIGHT_BRACKET,
            ],
            $evaluated
        );

        $evaluated = $lexer->evaluate(['#', 'comment']);
        $this->assertEquals(
            [
                Symbols::HASH,
                Symbols::IDENTIFIER,
            ],
            $evaluated
        );

        $evaluated = $lexer->evaluate(['comma', ',', 'separated', ',', 'values']);
        $this->assertEquals(
            [
                Symbols::IDENTIFIER,
                Symbols::COMMA,
                Symbols::IDENTIFIER,
                Symbols::COMMA,
                Symbols::IDENTIFIER,
            ],
            $evaluated
        );
    }

    public function testTokenize()
    {
        $fixture = <<<END
        [ITEM]
        id=flashlight
        size =2
        readable = yes
        END;

        $expected = [
            Symbols::LEFT_BRACKET,
            Symbols::IDENTIFIER,
            Symbols::RIGHT_BRACKET,

            Symbols::IDENTIFIER,
            Symbols::EQUALS,
            Symbols::IDENTIFIER,

            Symbols::IDENTIFIER,
            Symbols::EQUALS,
            Symbols::IDENTIFIER,

            Symbols::IDENTIFIER,
            Symbols::EQUALS,
            Symbols::IDENTIFIER,

        ];

        $lexer = new Lexer();
        $tokens = $lexer->tokenize($fixture);
        $this->assertEquals($expected, $tokens);
    }
}
