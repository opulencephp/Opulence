<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Views\Compilers\Fortune\Lexers;

use Opulence\Views\Compilers\Fortune\Lexers\Tokens\Token;
use Opulence\Views\Compilers\Fortune\Lexers\Tokens\TokenTypes;
use Opulence\Views\IView;
use RuntimeException;

/**
 * Defines the view lexer
 */
class Lexer implements ILexer
{
    /** @var string The current input being lexed */
    private $input = '';
    /** @var Token[] The list of tokens generated by the lexer */
    private $tokens = [];
    /** @var array The directive delimiters */
    private $directiveDelimiters = [];
    /** @var array The sanitized tag delimiters */
    private $sanitizedTagDelimiters = [];
    /** @var array The unsanitized tag delimiters */
    private $unsanitizedTagDelimiters = [];
    /** @var array The comment delimiters */
    private $commentDelimiters = [];
    /** @var int The cursor (current position) of the lexer */
    private $cursor = 0;
    /** @var int The current line the lexer is on */
    private $line = 1;
    /** @var string The buffer of any expressions that fall outside tags/directives */
    private $expressionBuffer = '';
    /** @var array Caches information for the current stream, which improves performance by 4x */
    private $streamCache = ['cursor' => PHP_INT_MAX, 'length' => null, 'stream' => ''];

    /**
     * @inheritdoc
     */
    public function lex(IView $view) : array
    {
        $this->initializeVars($view);
        $this->lexExpression();

        return $this->tokens;
    }

    /**
     * Gets whether or not we're at the end of the file
     *
     * @return bool True if we're at the end of the file, otherwise false
     */
    private function atEof() : bool
    {
        return $this->getStream() === '';
    }

    /**
     * Flushes the expression buffer
     */
    private function flushExpressionBuffer()
    {
        if ($this->expressionBuffer !== '') {
            $this->tokens[] = new Token(TokenTypes::T_EXPRESSION, $this->expressionBuffer, $this->line);
            // Account for all the new lines
            $this->line += substr_count($this->expressionBuffer, "\n");
            $this->expressionBuffer = '';
        }
    }

    /**
     * Gets the character under the cursor
     *
     * @return string The current character
     */
    private function getCurrentChar() : string
    {
        return $this->input[$this->cursor] ?? '';
    }

    /**
     * Gets a sorted mapping of opening statement delimiters to the lexing methods to call on a match
     *
     * @return array The mapping of opening statement delimiters to the methods
     */
    private function getStatementLexingMethods() : array
    {
        $statements = [
            $this->directiveDelimiters[0] => 'lexDirectiveStatement',
            $this->sanitizedTagDelimiters[0] => 'lexSanitizedTagStatement',
            $this->unsanitizedTagDelimiters[0] => 'lexUnsanitizedTagStatement',
            $this->commentDelimiters[0] => 'lexCommentStatement',
            '<?php' => 'lexPhpStatement',
            '<?' => 'lexPhpStatement'
        ];

        /**
         * In case one delimiter is a substring of the other ("{{" and "{{!"), we want to sort the delimiters
         * so that the longest delimiters come first
         */
        uksort($statements, function ($a, $b) {
            if (strlen($a) > strlen($b)) {
                return -1;
            } else {
                return 1;
            }
        });

        return $statements;
    }

    /**
     * Gets the stream of input that has not yet been lexed
     *
     * @param int|null $cursor The position of the cursor
     * @param int|null $length The length of input to return
     * @return string The stream of input
     */
    private function getStream(int $cursor = null, int $length = null) : string
    {
        if ($cursor === null) {
            $cursor = $this->cursor;
        }

        // If the cached length isn't the same or if the cursor has actually gone backwards, use the original input
        if ($this->streamCache['length'] !== $length || $this->streamCache['cursor'] > $cursor) {
            $this->streamCache['cursor'] = $cursor;
            $this->streamCache['length'] = $length;

            if ($length === null) {
                $this->streamCache['stream'] = substr($this->input, $cursor);
            } else {
                $this->streamCache['stream'] = substr($this->input, $cursor, $length);
            }
        } elseif ($this->streamCache['length'] === $length && $this->streamCache['cursor'] !== $cursor) {
            // Grab the substring from the cached stream
            $cursorDifference = $cursor - $this->streamCache['cursor'];

            if ($length === null) {
                $this->streamCache['stream'] = substr($this->streamCache['stream'], $cursorDifference);
            } else {
                $this->streamCache['stream'] = substr($this->streamCache['stream'], $cursorDifference, $length);
            }

            $this->streamCache['cursor'] = $cursor;
        }

        return $this->streamCache['stream'];
    }

    /**
     * Initializes instance variables for lexing
     *
     * @param IView $view The view that's being lexed
     */
    private function initializeVars(IView $view)
    {
        $this->directiveDelimiters = $view->getDelimiters(IView::DELIMITER_TYPE_DIRECTIVE);
        $this->sanitizedTagDelimiters = $view->getDelimiters(IView::DELIMITER_TYPE_SANITIZED_TAG);
        $this->unsanitizedTagDelimiters = $view->getDelimiters(IView::DELIMITER_TYPE_UNSANITIZED_TAG);
        $this->commentDelimiters = $view->getDelimiters(IView::DELIMITER_TYPE_COMMENT);
        // Normalize the line-endings
        $this->input = str_replace(["\r\n", "\r"], "\n", $view->getContents());
        $this->tokens = [];
        $this->cursor = 0;
        $this->line = 1;
        $this->expressionBuffer = '';
    }

    /**
     * Lexes a comment statement
     *
     * @throws RuntimeException Thrown if the statement has an invalid token
     */
    private function lexCommentStatement()
    {
        $this->lexDelimitedExpressionStatement(
            TokenTypes::T_COMMENT_OPEN,
            $this->commentDelimiters[0],
            TokenTypes::T_COMMENT_CLOSE,
            $this->commentDelimiters[1],
            false
        );
    }

    /**
     * Lexes an expression that is delimited with tags
     *
     * @param string $closeDelimiter The close delimiter
     */
    private function lexDelimitedExpression(string $closeDelimiter)
    {
        $expressionBuffer = '';
        $newLinesAfterExpression = 0;

        while (!$this->matches($closeDelimiter, false) && !$this->atEof()) {
            $currentChar = $this->getCurrentChar();

            if ($currentChar === "\n") {
                if (trim($expressionBuffer) === '') {
                    $this->line++;
                } else {
                    $newLinesAfterExpression++;
                }
            }

            $expressionBuffer .= $currentChar;
            $this->cursor++;
        }

        $expressionBuffer = trim($expressionBuffer);

        if ($expressionBuffer !== '') {
            $this->tokens[] = new Token(
                TokenTypes::T_EXPRESSION,
                $this->replaceViewFunctionCalls($expressionBuffer),
                $this->line
            );
            $this->line += $newLinesAfterExpression;
        }
    }

    /**
     * Lexes a statement that is comprised of a delimited statement
     *
     * @param string $openTokenType The open token type
     * @param string $openDelimiter The open delimiter
     * @param string $closeTokenType The close token type
     * @param string $closeDelimiter The close delimiter
     * @param bool $closeDelimiterOptional Whether or not the close delimiter is optional
     * @throws RuntimeException Thrown if the expression was not delimited correctly
     */
    private function lexDelimitedExpressionStatement(
        string $openTokenType,
        string $openDelimiter,
        string $closeTokenType,
        string $closeDelimiter,
        bool $closeDelimiterOptional
    ) {
        $this->flushExpressionBuffer();
        $this->tokens[] = new Token($openTokenType, $openDelimiter, $this->line);
        $this->lexDelimitedExpression($closeDelimiter);

        if (!$this->matches($closeDelimiter) && !$closeDelimiterOptional) {
            throw new RuntimeException(
                sprintf(
                    'Expected %s, found %s on line %d',
                    $closeDelimiter,
                    $this->getStream($this->cursor, strlen($closeDelimiter)),
                    $this->line
                )
            );
        }

        $this->tokens[] = new Token($closeTokenType, $closeDelimiter, $this->line);
    }

    /**
     * Lexes a directive statement
     *
     * @throws RuntimeException Thrown if there's an unmatched parenthesis
     */
    private function lexDirectiveExpression()
    {
        $this->lexDirectiveName();

        $parenthesisLevel = 0;
        $newLinesAfterExpression = 0;
        $expressionBuffer = '';

        while (!$this->matches($this->directiveDelimiters[1], false) && !$this->atEof()) {
            $currentChar = $this->getCurrentChar();

            if ($currentChar === '(') {
                $expressionBuffer .= $currentChar;
                $parenthesisLevel++;
            } elseif ($currentChar === ')') {
                $parenthesisLevel--;
                $expressionBuffer .= $currentChar;
            } elseif ($currentChar === "\n") {
                if (trim($expressionBuffer) === '') {
                    $this->line++;
                } else {
                    $newLinesAfterExpression++;
                }
            } else {
                $expressionBuffer .= $currentChar;
            }

            $this->cursor++;
        }

        if ($parenthesisLevel !== 0) {
            throw new RuntimeException(
                sprintf(
                    'Unmatched parenthesis on line %d',
                    $this->line
                )
            );
        }

        $expressionBuffer = trim($expressionBuffer);
        $expressionBuffer = $this->replaceViewFunctionCalls($expressionBuffer);

        if (!empty($expressionBuffer)) {
            $this->tokens[] = new Token(TokenTypes::T_EXPRESSION, $expressionBuffer, $this->line);
        }

        $this->line += $newLinesAfterExpression;
    }

    /**
     * Lexes a directive name
     *
     * @throws RuntimeException Thrown if the directive did not have a name
     */
    private function lexDirectiveName()
    {
        $name = '';
        $newLinesAfterName = 0;

        // Loop while there's still a directive name or until we encounter the first space after the name
        do {
            $currentChar = $this->getCurrentChar();

            // Handle new line characters between directive delimiters
            if ($currentChar === "\n") {
                if (trim($name) === '') {
                    $this->line++;
                } else {
                    $newLinesAfterName++;
                }
            }

            $name .= $currentChar;
            $this->cursor++;
        } while (
            preg_match("/^[a-zA-Z0-9_\s]$/", $this->getCurrentChar()) === 1 &&
            ($this->getCurrentChar() !== ' ' || trim($name) === '')
        );

        $name = trim($name);

        if ($name === '') {
            throw new RuntimeException(
                sprintf(
                    'Expected %s on line %d, none found',
                    TokenTypes::T_DIRECTIVE_NAME,
                    $this->line
                )
            );
        }

        $this->tokens[] = new Token(TokenTypes::T_DIRECTIVE_NAME, $name, $this->line);
        $this->line += $newLinesAfterName;
    }

    /**
     * Lexes a directive statement
     *
     * @throws RuntimeException Thrown if the statement has an invalid token
     */
    private function lexDirectiveStatement()
    {
        $this->flushExpressionBuffer();
        $this->tokens[] = new Token(TokenTypes::T_DIRECTIVE_OPEN, $this->directiveDelimiters[0], $this->line);
        $this->lexDirectiveExpression();

        if (!$this->matches($this->directiveDelimiters[1])) {
            throw new RuntimeException(
                sprintf(
                    'Expected %s, found %s on line %d',
                    $this->directiveDelimiters[1],
                    $this->getStream($this->cursor, strlen($this->directiveDelimiters[1])),
                    $this->line
                )
            );
        }

        $this->tokens[] = new Token(TokenTypes::T_DIRECTIVE_CLOSE, $this->directiveDelimiters[1], $this->line);
    }

    /**
     * Lexes an expression
     *
     * @throws RuntimeException Thrown if there was an invalid token
     */
    private function lexExpression()
    {
        $statementMethods = $this->getStatementLexingMethods();

        while (!$this->atEof()) {
            reset($statementMethods);
            $matchedStatement = false;

            // This is essentially a foreach loop that can be reset
            while (list($statementOpenDelimiter, $methodName) = $this->eachPolyfill($statementMethods)) {
                if ($this->matches($statementOpenDelimiter)) {
                    // This is an unescaped statement
                    $matchedStatement = true;
                    $this->{$methodName}();

                    // Now that we've matched, we want to reset the loop so that longest delimiters are matched first
                    reset($statementMethods);
                } elseif ($this->getCurrentChar() === '\\') {
                    // Now that we know we're on an escape character, spend the resources to check for a match
                    if ($this->matches("\\$statementOpenDelimiter")) {
                        // This is an escaped statement
                        $this->expressionBuffer .= $statementOpenDelimiter;
                    }
                }
            }

            // Handle any text outside statements
            if (!$matchedStatement && !$this->atEof()) {
                $currentChar = $this->getCurrentChar();
                $this->expressionBuffer .= $currentChar;
                $this->cursor++;

                // Keep on going if we're seeing alphanumeric text
                while (ctype_alnum($this->getCurrentChar())) {
                    $currentChar = $this->getCurrentChar();
                    $this->expressionBuffer .= $currentChar;
                    $this->cursor++;
                }
            } else {
                $this->flushExpressionBuffer();
            }
        }

        // Flush anything left over in the buffer
        $this->flushExpressionBuffer();
    }

    /**
     * Lexes a PHP statement
     *
     * @throws RuntimeException Thrown if the statement was not delimited correctly
     */
    private function lexPhpStatement()
    {
        $this->lexDelimitedExpressionStatement(
            TokenTypes::T_PHP_TAG_OPEN,
            '<?php',
            TokenTypes::T_PHP_TAG_CLOSE,
            '?>',
            true
        );
    }

    /**
     * Lexes a sanitized tag statement
     *
     * @throws RuntimeException Thrown if the statement has an invalid token
     */
    private function lexSanitizedTagStatement()
    {
        $this->lexDelimitedExpressionStatement(
            TokenTypes::T_SANITIZED_TAG_OPEN,
            $this->sanitizedTagDelimiters[0],
            TokenTypes::T_SANITIZED_TAG_CLOSE,
            $this->sanitizedTagDelimiters[1],
            false
        );
    }

    /**
     * Lexes an unsanitized tag statement
     *
     * @throws RuntimeException Thrown if the statement has an invalid token
     */
    private function lexUnsanitizedTagStatement()
    {
        $this->lexDelimitedExpressionStatement(
            TokenTypes::T_UNSANITIZED_TAG_OPEN,
            $this->unsanitizedTagDelimiters[0],
            TokenTypes::T_UNSANITIZED_TAG_CLOSE,
            $this->unsanitizedTagDelimiters[1],
            false
        );
    }

    /**
     * Gets whether or not the input at the cursor matches an expected value
     *
     * @param string $expected The expected string
     * @param bool $shouldConsume Whether or not to consume the expected value on a match
     * @param int|null $cursor The cursor position to match at
     * @return bool True if the input at the cursor matches the expected value, otherwise false
     */
    private function matches(string $expected, bool $shouldConsume = true, int $cursor = null) : bool
    {
        $stream = $this->getStream($cursor);
        $expectedLength = strlen($expected);

        if (substr($stream, 0, $expectedLength) == $expected) {
            if ($shouldConsume) {
                $this->cursor += $expectedLength;
            }

            return true;
        }

        return false;
    }

    /**
     * Replaces view function calls with valid PHP calls
     *
     * @param string $expression The expression to replace calls in
     * @return string The expression with replaced calls
     */
    private function replaceViewFunctionCalls(string $expression) : string
    {
        $phpTokens = token_get_all('<?php ' . $expression . ' ?>');
        $opulenceTokens = [];

        // This is essentially a foreach loop that can be fast-forwarded
        while (list($index, $token) = $this->eachPolyfill($phpTokens)) {
            if (is_string($token)) {
                // Convert the simple token to an array for uniformity
                $opulenceTokens[] = [T_STRING, $token, 0];

                continue;
            }

            switch ($token[0]) {
                case T_STRING:
                    // If this is a function
                    if (count($phpTokens) > $index && $phpTokens[$index + 1] === '(') {
                        $prevToken = $index > 0 ? $phpTokens[$index - 1] : null;

                        // If this is a native PHP function or is really a method call, don't convert it
                        if (
                            (
                                ($prevToken[0] === T_OBJECT_OPERATOR || $prevToken[0] === T_DOUBLE_COLON) &&
                                is_array($prevToken)
                            ) ||
                            function_exists($token[1])
                        ) {
                            $opulenceTokens[] = $token;
                        } else {
                            // This is a view function
                            // Add $__opulenceFortuneTranspiler
                            $opulenceTokens[] = [T_VARIABLE, '$__opulenceFortuneTranspiler', $token[2]];
                            // Add ->
                            $opulenceTokens[] = [T_OBJECT_OPERATOR, '->', $token[2]];
                            // Add callViewFunction("FUNCTION_NAME")
                            $opulenceTokens[] = [T_STRING, 'callViewFunction("' . $token[1] . '")', $token[2]];
                        }
                    } else {
                        $opulenceTokens[] = $token;
                    }

                    break;
                default:
                    $opulenceTokens[] = $token;

                    break;
            }
        }

        // Remove php open/close PHP tags
        array_shift($opulenceTokens);
        array_pop($opulenceTokens);

        // Rejoin the tokens
        $joinedTokens = implode('', array_column($opulenceTokens, 1));

        $replacementCount = 0;
        $callback = function (array $matches) {
            if ($matches[2] === ')') {
                // There were no parameters
                return $matches[1] . ')';
            } else {
                // There were parameters
                return $matches[1] . ', ' . $matches[2];
            }
        };

        /**
         * View functions are left in a broken state
         * For example, 'foo()' will currently look like '...->callViewFunction("foo")()'
         * This should be converted to '...->callViewFunction("foo")'
         * Similarly, 'foo("bar")' will currently look like '...->callViewFunction("foo")("bar")'
         * This should be converted to '...->callViewFunction("foo", "bar")'
         */
        do {
            $joinedTokens = preg_replace_callback(
                '/(\$__opulenceFortuneTranspiler->callViewFunction\([^\)]+)\)\((.)/',
                $callback,
                $joinedTokens,
                -1,
                $replacementCount
            );
        } while ($replacementCount > 0);

        return trim($joinedTokens);
    }

    /**
     * Polyfill for PHP each, which is now deprecated
     *
     * @param array $arr
     *
     * @return array|null
     */
    private function eachPolyfill(array &$arr)
    {
        $key   = key($arr);
        $value = null;
        if ($key !== null) {
            $value = [$key, current($arr), 'key' => $key, 'value' => current($arr)];
        }

        next($arr);

        return $value;
    }
}
