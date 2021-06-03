<?php

namespace AdventureGameMarkupLanguage\Syntax;

/**
 * Interface SyntaxInterface defines methods for Syntax.
 * @package AdventureGameMarkupLanguage\Syntax
 */
interface SyntaxInterface
{
    /**
     * Render original syntax string.
     * @return string
     */
    public function toString(): string;
}