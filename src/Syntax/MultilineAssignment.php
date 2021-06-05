<?php

namespace AdventureGameMarkupLanguage\Syntax;

/**
 * Class MultilineAssignment is a multi-line identifier assignment designated by a section.
 * @package AdventureGameMarkupLanguage\Syntax
 */
class MultilineAssignment extends AbstractSyntax
{
    public function __construct(protected string $section, protected array $lines)
    {
    }

    /**
     * Get the lines.
     * @return array
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * Get the section value.
     * @return string
     */
    public function getSection(): string
    {
        return $this->section;
    }

    public function toString(): string
    {
        $lines = [];

        $section = [self::TOKEN_TYPE_OPEN, $this->getSection(), self::TOKEN_TYPE_CLOSE];
        $lines[] = implode('', $section);

        foreach ($this->getLines() as $line) {
            $lines[] = implode('', $line);
        }

        return implode("\n", $lines);
    }
}
