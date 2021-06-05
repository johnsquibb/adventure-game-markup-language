<?php

namespace AdventureGameMarkupLanguage\Syntax;

/**
 * Class ListAssignment is a CSV of identifier values.
 * @package AdventureGameMarkupLanguage\Syntax
 */
class ListAssignment extends Assignment
{
    public function toString(): string
    {
        $value = implode(',', $this->getMergedValues());
        $parts = [$this->variable, self::TOKEN_ASSIGNMENT, $value];
        return implode('', $parts);
    }

    /**
     * Merge the CSV values.
     * @return array
     */
    public function getMergedValues(): array
    {
        $merged = [];

        foreach ($this->getValues() as $value) {
            if (is_array($value)) {
                $merged[] = implode('', $value);
            } else {
                $merged[] = $value;
            }
        }

        return $merged;
    }
}