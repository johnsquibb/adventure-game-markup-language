<?php

namespace AdventureGameMarkupLanguage\Syntax;

/**
 * Type is a type declaration.
 * Class Type
 * @package AdventureGameMarkupLanguage\Syntax
 */
class Type extends AbstractSyntax
{
    public function __construct(private string $identifier)
    {
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function toString(): string
    {
        $parts = [self::TOKEN_TYPE_OPEN, $this->identifier, self::TOKEN_TYPE_CLOSE];

        return implode('', $parts);
    }
}