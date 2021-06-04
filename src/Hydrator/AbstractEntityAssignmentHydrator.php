<?php

namespace AdventureGameMarkupLanguage\Hydrator;

abstract class AbstractEntityAssignmentHydrator implements AssignmentHydratorInterface
{
    // Generic
    private string $id = '';
    private string $name = '';
    private int $size = 0;
    private bool $readable = false;
    private bool $activatable = false;
    private bool $accessible = false;
    private bool $acquirable = false;
    private bool $deactivatable = false;
    private bool $discoverable = false;
    private bool $mutable = false;
    private bool $revealed = false;
    private array $tags = [];
    private array $phrases = [];
    private array $description = [];
    private array $text = [];

    // Containers, Locations
    private int $capacity = 0;
    private array $items = [];

    // Locations
    private array $exits = [];

    // Portals
    private string $destination = '';
    private string $direction = '';

    // Portals, Containers
    private string $key = '';
    private bool $lockable = false;
    private bool $locked = false;

    public function assign(string $variable, array $values): void
    {
        switch ($variable) {
            case 'id':
                $this->id = $this->firstValue($values);
                break;
            case 'name':
                $this->name = $this->joinValues($values);
                break;
            case 'size':
                $this->size = $this->intValue($values);
                break;
            case 'readable':
                $this->readable = $this->boolValue($values);
                break;
            case 'activatable':
                $this->activatable = $this->boolValue($values);
                break;
            case 'accessible':
                $this->accessible = $this->boolValue($values);
                break;
            case 'acquirable':
                $this->acquirable = $this->boolValue($values);
                break;
            case 'deactivatable':
                $this->deactivatable = $this->boolValue($values);
                break;
            case 'discoverable':
                $this->discoverable = $this->boolValue($values);
                break;
            case 'mutable':
                $this->mutable = $this->boolValue($values);
                break;
            case 'lockable':
                $this->lockable = $this->boolValue($values);
                break;
            case 'locked':
                $this->locked = $this->boolValue($values);
                break;
            case 'revealed':
                $this->revealed = $this->boolValue($values);
                break;
            case 'tags':
                $this->tags = $values;
                break;
            case 'description':
                $this->description = $this->joinLines($values);
                break;
            case 'phrases':
                $this->phrases = $values;
                break;
            case 'text':
                $this->text = $this->joinLines($values);
                break;
            case 'capacity':
                $this->capacity = $this->intValue($values);
                break;
            case 'items':
                $this->items = $values;
                break;
            case 'exits':
                $this->exits = $values;
                break;
            case 'destination':
                $this->destination = $this->firstValue($values);
                break;
            case 'direction':
                $this->direction = $this->firstValue($values);
                break;
            case 'key':
                $this->key = $this->firstValue($values);
                break;
        }
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getExits(): array
    {
        return $this->exits;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getReadable(): bool
    {
        return $this->readable;
    }

    public function getActivatable(): bool
    {
        return $this->activatable;
    }

    public function getAccessible(): bool
    {
        return $this->accessible;
    }

    public function getAcquirable(): bool
    {
        return $this->acquirable;
    }

    public function getDeactivatable(): bool
    {
        return $this->deactivatable;
    }

    public function getDiscoverable(): bool
    {
        return $this->discoverable;
    }

    public function getMutable(): bool
    {
        return $this->mutable;
    }

    public function getLockable(): bool
    {
        return $this->lockable;
    }

    public function getLocked(): bool
    {
        return $this->locked;
    }

    public function getRevealed(): bool
    {
        return $this->revealed;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getPhrases(): array
    {
        return $this->phrases;
    }

    public function getDescription(): array
    {
        return $this->description;
    }

    public function getText(): array
    {
        return $this->text;
    }

    /**
     * Get the first value from list.
     * @param array $values
     * @return string
     */
    protected function firstValue(array $values): string
    {
        return $values[0] ?? '';
    }

    /**
     * Get the first value as boolean from list.
     * @param array $values
     * @return bool
     */
    protected function boolValue(array $values): bool
    {
        return $this->firstValue($values) === 'yes';
    }

    /**
     * Get the first value as integer from list.
     * @param array $values
     * @return int
     */
    protected function intValue(array $values): int
    {
        return (int)$this->firstValue($values);
    }

    /**
     * Join list of values into single string.
     * @param array $values
     * @return string
     */
    protected function joinValues(array $values): string
    {
        return implode(' ', $values);
    }

    /**
     * Join line values into strings.
     * @param array $values
     * @return array
     */
    protected function joinLines(array $values): array
    {
        $lines = [];
        foreach ($values as $line) {
            $lines[] = $this->joinValues($line);
        }

        return $lines;
    }
}