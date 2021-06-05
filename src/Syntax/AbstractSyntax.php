<?php

namespace AdventureGameMarkupLanguage\Syntax;

/**
 * Class AbstractSyntax provides constants for syntax.
 * @package AdventureGameMarkupLanguage\Syntax
 */
abstract class AbstractSyntax implements SyntaxInterface
{
    public const TOKEN_ASSIGNMENT = '=';
    public const TOKEN_COMMENT = '#';
    public const TOKEN_DELIMITER = ',';
    public const TOKEN_ESCAPE = '\\';
    public const TOKEN_NEWLINE = "\n";
    public const TOKEN_NOTHING = '';
    public const TOKEN_SPACE = ' ';
    public const TOKEN_TYPE_CLOSE = ']';
    public const TOKEN_TYPE_OPEN = '[';
}