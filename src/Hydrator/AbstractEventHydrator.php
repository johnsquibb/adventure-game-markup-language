<?php

namespace AdventureGameMarkupLanguage\Hydrator;

abstract class AbstractEventHydrator extends AbstractEntityAssignmentHydrator
{
    private string $type = '';
    private string $trigger = '';

    public function assign(string $variable, array $values): void
    {
        switch ($variable) {
            case 'type':
                $this->type = $this->firstValue($values);
                break;
            case 'trigger':
                $this->trigger = $this->firstValue($values);
                break;
            default:
                parent::assign($variable, $values);
        }
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