<?php

namespace AdventureGameMarkupLanguage;

use AdventureGameMarkupLanguage\Syntax\AbstractSyntax;
use AdventureGameMarkupLanguage\Syntax\Assignment;
use AdventureGameMarkupLanguage\Syntax\Identifier;
use AdventureGameMarkupLanguage\Syntax\ListAssignment;
use AdventureGameMarkupLanguage\Syntax\MultilineAssignment;
use AdventureGameMarkupLanguage\Syntax\Type;

/**
 * Class Lexer provides lexical analysis methods for AGML syntax.
 * @package AdventureGameMarkupLanguage
 */
class Lexer
{
    /**
     * Create the syntax tree from the tokens.
     * @param array $tokens
     * @return SyntaxTree
     */
    public function createSyntaxTree(array $tokens): SyntaxTree
    {
        $tokens = $this->preprocessTokens($tokens);

        $tree = new SyntaxTree();

        $index = 0;
        while ($index < count($tokens)) {
            switch ($tokens[$index]) {
                case Symbols::IDENTIFIER:
                    $index++;
                    $identifier = $tokens[$index];
                    $index++;
                    // Is it an assignment?
                    if ($this->peekToken($index, $tokens) === Symbols::EQUALS) {
                        // Get the first assignment.
                        $index++;
                        $values = [];
                        while (
                            $this->peekToken($index, $tokens) === Symbols::IDENTIFIER
                            // The identifier is not the start of an assignment.
                            && $this->peekToken($index + 2, $tokens) !== Symbols::EQUALS
                        ) {
                            $index++;
                            $values[] = $tokens[$index];
                            $index++;
                        }

                        // If it's a list assignment, grab the additional assignments.
                        if ($this->peekToken($index, $tokens) === Symbols::COMMA) {
                            $list = [count($values) > 1 ? $values : $values[0]];

                            while ($this->peekToken($index, $tokens) === Symbols::COMMA) {
                                $index++;
                                $csvValues = [];
                                while ($this->peekToken($index, $tokens) === Symbols::IDENTIFIER) {
                                    $index++;
                                    $csvValues[] = $tokens[$index];
                                    $index++;
                                }
                                $list[] = count($csvValues) > 1 ? $csvValues : $csvValues[0];
                            }
                            $node = new ListAssignment($identifier, $list);
                        } else {
                            $node = new Assignment($identifier, $values);
                        }
                    } else {
                        $node = new Identifier($identifier);
                    }

                    $tree->addNode($node);
                    break;
                case Symbols::LEFT_BRACKET:
                    $index++;
                    if ($this->peekToken($index, $tokens) === Symbols::IDENTIFIER) {
                        $index++;
                    }

                    $type = $tokens[$index];
                    $index++;

                    if ($this->peekToken($index, $tokens) === Symbols::RIGHT_BRACKET) {
                        $index++;
                    }

                    // All uppercase is a type.
                    if (strtoupper($type) === $type) {
                        $node = new Type($type);
                    } else {
                        // Advance past new line following section declaration.
                        $index++;

                        // Anything else is a multiline description.
                        $lines = [];
                        $identifiers = [];

                        while (
                            $this->peekToken($index, $tokens) === Symbols::IDENTIFIER
                            || $this->peekToken($index, $tokens) === Symbols::NEWLINE
                        ) {
                            if ($this->peekToken($index, $tokens) === Symbols::IDENTIFIER) {
                                $index++;
                                // Append to line.
                                $identifiers[] = $tokens[$index];
                            } else {
                                // Add line, reset.
                                $lines[] = $identifiers;
                                $identifiers = [];
                            }

                            $index++;
                        }

                        $node = new MultilineAssignment($type, $lines);
                    }

                    $tree->addNode($node);
                    break;
                default:
                    $index++;
            }
        }

        return $tree;
    }

    /**
     * Preprocess tokens to deal with escape characters, spaces.
     * @param array $tokens
     * @return array
     */
    private function preprocessTokens(array $tokens): array
    {
        $tokens = $this->convertTokensToIdentifiers($tokens);

        $index = 0;
        while ($index < count($tokens)) {
            switch ($tokens[$index]) {
                case Symbols::ESCAPE:
                    $literal = match ($tokens[$index + 1]) {
                        Symbols::EQUALS => AbstractSyntax::TOKEN_ASSIGNMENT,
                        Symbols::LEFT_BRACKET => AbstractSyntax::TOKEN_TYPE_OPEN,
                        Symbols::RIGHT_BRACKET => AbstractSyntax::TOKEN_TYPE_CLOSE,
                        Symbols::COMMA => AbstractSyntax::TOKEN_DELIMITER,
                        Symbols::ESCAPE => AbstractSyntax::TOKEN_ESCAPE,
                        Symbols::HASH => AbstractSyntax::TOKEN_COMMENT,
                        // no break
                        default => AbstractSyntax::TOKEN_NOTHING,
                    };

                    // Convert the current token to its own identifier.
                    $tokens[$index] = Symbols::IDENTIFIER;
                    $tokens[$index + 1] = $literal;

                    break;
                default:
                    $index++;
            }
        }

        return $tokens;
    }

    /**
     * Convert special tokens to identifiers.
     * @param array $tokens
     * @return array
     */
    private function convertTokensToIdentifiers(array $tokens): array
    {
        $converted = [];

        $index = 0;
        while ($index < count($tokens)) {
            switch ($tokens[$index]) {
                case Symbols::SPACE:
                    $converted[] = Symbols::IDENTIFIER;
                    $converted[] = AbstractSyntax::TOKEN_SPACE;
                    break;
                case Symbols::HASH:
                    $converted[] = Symbols::IDENTIFIER;
                    $converted[] = AbstractSyntax::TOKEN_COMMENT;
                    break;
                default:
                    $converted[] = $tokens[$index];
                    break;
            }
            $index++;
        }

        return $converted;
    }

    /**
     * Peek at the token.
     * @param int $index
     * @param array $tokens
     * @return string
     */
    public function peekToken(int $index, array $tokens): string
    {
        if (isset($tokens[$index])) {
            return $tokens[$index];
        }

        return AbstractSyntax::TOKEN_NOTHING;
    }

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
            $symbols = preg_split(
                "/(\\\)|(,)|(#)|(\[)|(])|(=)|(\s)/",
                trim($line),
                -1,
                PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
            );

            // Sometimes, it feels like the parser is ignoring all my comments.
            if (isset($symbols[0]) && $symbols[0] === AbstractSyntax::TOKEN_COMMENT) {
                continue;
            }

            array_push($lexemes, ...$symbols);
            $lexemes[] = AbstractSyntax::TOKEN_NEWLINE;
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
            array_push($analyzed, ...$this->analyzeLexeme($lexeme));
        }

        return $analyzed;
    }

    /**
     * Analyze a lexeme.
     * @param string $lexeme
     * @return array
     */
    private function analyzeLexeme(string $lexeme): array
    {
        return match ($lexeme) {
            AbstractSyntax::TOKEN_SPACE => [Symbols::SPACE],
            AbstractSyntax::TOKEN_ESCAPE => [Symbols::ESCAPE],
            AbstractSyntax::TOKEN_DELIMITER => [Symbols::COMMA],
            AbstractSyntax::TOKEN_COMMENT => [Symbols::HASH],
            AbstractSyntax::TOKEN_ASSIGNMENT => [Symbols::EQUALS],
            AbstractSyntax::TOKEN_TYPE_OPEN => [Symbols::LEFT_BRACKET],
            AbstractSyntax::TOKEN_TYPE_CLOSE => [Symbols::RIGHT_BRACKET],
            AbstractSyntax::TOKEN_NEWLINE => [Symbols::NEWLINE],
            default => [Symbols::IDENTIFIER, $lexeme],
        };
    }
}
