<?php

namespace AdventureGameMarkupLanguage;

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