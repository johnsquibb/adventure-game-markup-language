<?php

namespace AdventureGameMarkupLanguage\Hydrator;

abstract class AbstractTriggerHydrator extends AbstractEntityAssignmentHydrator
{
    private string $type = '';
    private int $uses = 0;
    private array $activators = [];
    private array $comparisons = [];
    private string $item = '';
    private string $location = '';
    private string $portal = '';

    public function assign(string $variable, array $values): void
    {
        switch ($variable) {
            case 'type':
                $this->type = $this->firstValue($values);
                break;
            case 'uses':
                $this->uses = $this->intValue($values);
                break;
            case 'activators':
                $this->activators = $this->joinListValues($values);
                break;
            case 'comparisons':
                $this->comparisons = $this->joinListValues($values);
                break;
            case 'item':
                $this->item = $this->firstValue($values);
                break;
            case 'location':
                $this->location = $this->firstValue($values);
                break;
            case 'portal':
                $this->portal = $this->firstValue($values);
                break;
            default:
                parent::assign($variable, $values);
        }
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUses(): int
    {
        return $this->uses;
    }

    public function getActivators(): array
    {
        return $this->activators;
    }

    public function getComparisons(): array
    {
        return $this->comparisons;
    }

    public function getItem(): string
    {
        return $this->item;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getPortal(): string
    {
        return $this->portal;
    }
}