<?php

namespace AdventureGameMarkupLanguage\Hydrator;

interface AssignmentHydratorInterface
{
    public function assign(string $variable, array $values): void;
}