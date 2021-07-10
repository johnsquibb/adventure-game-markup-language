<?php

namespace AdventureGameMarkupLanguage\Syntax;

/**
 * Class Assignment is a variable, value assignment.
 * @package AdventureGameMarkupLanguage\Syntax
 */
class Assignment extends AbstractSyntax
{
    public function __construct(protected string $variable, protected array $values)
    {
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getVariable(): string
    {
        return $this->variable;
    }

    public function toString(): string
    {
        $value = implode('', $this->values);
        $parts = [$this->variable, self::TOKEN_ASSIGNMENT, $value];
        return implode('', $parts);
    }
}
