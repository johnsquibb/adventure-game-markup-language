<?php

namespace AdventureGameMarkupLanguage\Syntax;

/**
 * Class Comment is a comment.
 * @package AdventureGameMarkupLanguage\Syntax
 */
class Comment extends AbstractSyntax
{
    public function __construct(private array $identifiers = [])
    {
    }

    public function getIdentifiers(): array
    {
        return $this->identifiers;
    }

    public function toString(): string
    {
        $parts = $this->identifiers;
        array_unshift($parts, self::TOKEN_COMMENT);

        return implode('', $parts);
    }
}