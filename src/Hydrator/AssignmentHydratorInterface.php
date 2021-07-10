<?php

namespace AdventureGameMarkupLanguage\Hydrator;

interface AssignmentHydratorInterface extends HydratorInterface
{
    public function assign(string $variable, array $values): void;
}
