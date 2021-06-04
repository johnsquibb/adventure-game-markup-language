<?php

namespace AdventureGameMarkupLanguage\Hydrator;

interface HydratorInterface
{
    public function assign(string $variable, array $values): void;
}