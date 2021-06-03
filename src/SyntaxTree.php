<?php

namespace AdventureGameMarkupLanguage;

use AdventureGameMarkupLanguage\Syntax\SyntaxInterface;

class SyntaxTree
{
    private array $nodes = [];

    public function getNodes(): array
    {
        return $this->nodes;
    }

    public function addNode(SyntaxInterface $syntax): void
    {
        $this->nodes[] = $syntax;
    }
}