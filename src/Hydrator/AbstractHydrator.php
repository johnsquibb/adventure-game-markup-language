<?php

namespace AdventureGameMarkupLanguage\Hydrator;

abstract class AbstractHydrator implements HydratorInterface
{
    protected function firstValue(array $values): string
    {
        return $values[0] ?? '';
    }

    protected function boolValue(array $values): bool
    {
        return $this->firstValue($values) === 'yes';
    }

    protected function intValue(array $values): int
    {
        return (int)$this->firstValue($values);
    }

    protected function joinValues(array $values): string
    {
        return implode(' ', $values);
    }

    protected function joinLines(array $values): array
    {
        $lines = [];
        foreach ($values as $line) {
            $lines[] = $this->joinValues($line);
        }

        return $lines;
    }
}