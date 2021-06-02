<?php

namespace AdventureGameMarkupLanguage;

/**
 * Class Lexer provides lexical analysis methods for AGML syntax.
 * @package AdventureGameMarkupLanguage
 */
class Lexer
{
    /**
     * Runs lexical analysis on string sequence and returns list of tokens.
     * @param string $sequence
     * @return array
     */
    public function tokenize(string $sequence): array
    {
        $lexemes = $this->scan($sequence);
        return $this->evaluate($lexemes);
    }

    /**
     * Scans string sequence for lexemes and returns the list.
     * @param string $sequence
     * @return array
     */
    public function scan(string $sequence): array
    {
        $sequence = trim($sequence);
        if (empty($sequence)) {
            return [];
        }

        $lines = explode("\n", $sequence);
        if (empty($lines)) {
            return [];
        }

        $lexemes = [];

        foreach ($lines as $line) {
            array_push($lexemes, ...preg_split(
                "/(,)|(#)|(\[)|(])|(=)|\s+/",
                trim($line),
                -1,
                PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
            ));
        }

        return $lexemes;
    }

    /**
     * Evaluates list of lexemes and returns list of matching symbols.
     * @param array $lexemes
     * @return array
     */
    public function evaluate(array $lexemes): array
    {
        $analyzed = [];

        foreach ($lexemes as $lexeme) {
            $analyzed[] = $this->analyzeLexeme($lexeme);
        }

        return $analyzed;
    }

    private function analyzeLexeme(string $lexeme): string
    {
        return match ($lexeme) {
            ',' => Symbols::COMMA,
            '#' => Symbols::HASH,
            '=' => Symbols::EQUALS,
            '[' => Symbols::LEFT_BRACKET,
            ']' => Symbols::RIGHT_BRACKET,
            default => Symbols::IDENTIFIER,
        };
    }
}