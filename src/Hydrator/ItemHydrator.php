<?php

namespace AdventureGameMarkupLanguage\Hydrator;

class ItemHydrator extends AbstractHydrator
{
    private string $id = '';
    private string $name = '';
    private int $size = 0;
    private bool $readable = false;
    private bool $activatable = false;
    private bool $acquirable = false;
    private bool $deactivatable = false;
    private array $tags = [];
    private array $phrases = [];
    private array $description = [];
    private array $text = [];

    public function assign(string $variable, array $values): void
    {
        switch ($variable) {
            case 'id':
                $this->id = $this->firstValue($values);
                break;
            case 'name':
                $this->name = $this->joinValues($values);
                break;
            case 'description':
                $this->description = $this->joinLines($values);
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
            case 'acquirable':
                $this->acquirable = $this->boolValue($values);
                break;
            case 'deactivatable':
                $this->deactivatable = $this->boolValue($values);
                break;
            case 'tags':
                $this->tags = $values;
                break;
            case 'phrases':
                $this->phrases = $values;
                break;
            case 'text':
                $this->text = $this->joinLines($values);
                break;
        }
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

    public function getAcquirable(): bool
    {
        return $this->acquirable;
    }

    public function getDeactivatable(): bool
    {
        return $this->deactivatable;
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
}