<?php

namespace AdventureGameMarkupLanguage\Syntax;

/**
 * Class Identifier is a word, variable, or value in an assignment.
 * @package AdventureGameMarkupLanguage\Syntax
 */
class Identifier extends AbstractSyntax
{
    public function __construct(private string $value)
    {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
