<?php

namespace AdventureGameMarkupLanguage\Hydrator;

abstract class AbstractEventHydrator extends AbstractEntityAssignmentHydrator
{
    private string $type = '';
    private string $trigger = '';
    private string $item = '';
    private string $location = '';

    public function assign(string $variable, array $values): void
    {
        switch ($variable) {
            case 'type':
                $this->type = $this->firstValue($values);
                break;
            case 'trigger':
                $this->trigger = $this->firstValue($values);
                break;
            case 'item':
                $this->item = $this->firstValue($values);
                break;
            case 'location':
                $this->location = $this->firstValue($values);
                break;
            default:
                parent::assign($variable, $values);
        }
    }

    public function getItem(): string
    {
        return $this->item;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTrigger(): string
    {
        return $this->trigger;
    }
}