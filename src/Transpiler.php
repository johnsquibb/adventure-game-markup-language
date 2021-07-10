<?php

namespace AdventureGameMarkupLanguage;

/**
 * Class Transpiler processes AML text into hydrator objects using the lexer and parser.
 * @package AdventureGameMarkupLanguage
 */
class Transpiler
{
    public function __construct(private Lexer $lexer, private Parser $parser)
    {
    }

    /**
     * Transpile AGML text into hydrator objects.
     * @param string $markup
     * @return array
     * @throws Exception\InvalidTypeException
     */
    public function transpile(string $markup): array
    {
        $tokens = $this->lexer->tokenize($markup);
        $tree = $this->lexer->createSyntaxTree($tokens);

        return $this->parser->parseTree($tree);
    }
}
