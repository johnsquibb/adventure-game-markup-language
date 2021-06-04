<?php

namespace AdventureGameMarkupLanguage\Hydrator;

class AbstractEventHydrator
{
    // Triggers
    private array $activators = [];
    private array $comparisons = [];
    private string $portal = '';

    // Events
    private string $trigger = '';
    private string $item = '';
    private string $location = '';
    private int $uses = 0;
}