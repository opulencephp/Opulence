<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Responses\Compilers\Lexers;

use Opulence\Console\Responses\Compilers\Lexers\Tokens\Token;
use Opulence\Console\Responses\Compilers\Lexers\Tokens\TokenTypes;
use RuntimeException;

/**
 * Defines the response lexer
 */
class Lexer implements ILexer
{
    /**
     * @inheritdoc
     */
    public function lex(string $text) : array
    {
        $tokens = [];
        $wordBuffer = "";
        $elementNameBuffer = "";
        $inOpenTag = false;
        $inCloseTag = false;
        $charArray = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $textLength = count($charArray);

        foreach ($charArray as $charIter => $char) {
            switch ($char) {
                case "<":
                    if ($this->lookBehind($charArray, $charIter) === "\\") {
                        // This tag was escaped
                        // Don't include the preceding slash
                        $wordBuffer = mb_substr($wordBuffer, 0, -1) . $char;
                    } elseif ($inOpenTag || $inCloseTag) {
                        throw new RuntimeException(
                            sprintf(
                                "Invalid tags near \"%s\", character #%d",
                                $this->getSurroundingText($charArray, $charIter),
                                $charIter
                            )
                        );
                    } else {

                        // Check if this is a closing tag
                        if ($this->peek($charArray, $charIter) === "/") {
                            $inCloseTag = true;
                            $inOpenTag = false;
                        } else {
                            $inCloseTag = false;
                            $inOpenTag = true;
                        }

                        // Flush the word buffer
                        if ($wordBuffer !== "") {
                            $tokens[] = new Token(
                                TokenTypes::T_WORD,
                                $wordBuffer,
                                $charIter - mb_strlen($wordBuffer)
                            );
                            $wordBuffer = "";
                        }
                    }

                    break;
                case ">":
                    if ($inOpenTag || $inCloseTag) {
                        if ($inOpenTag) {
                            $tokens[] = new Token(
                                TokenTypes::T_TAG_OPEN,
                                $elementNameBuffer,
                                // Need to get the position of the beginning of the open tag
                                $charIter - mb_strlen($elementNameBuffer) - 1
                            );
                        } else {
                            $tokens[] = new Token(
                                TokenTypes::T_TAG_CLOSE,
                                $elementNameBuffer,
                                // Need to get the position of the beginning of the close tag
                                $charIter - mb_strlen($elementNameBuffer) - 2
                            );
                        }

                        $elementNameBuffer = "";
                        $inOpenTag = false;
                        $inCloseTag = false;
                    } else {
                        $wordBuffer .= $char;
                    }

                    break;
                default:
                    if ($inOpenTag || $inCloseTag) {
                        // We're in a tag, so buffer the element name
                        if ($char !== "/") {
                            $elementNameBuffer .= $char;
                        }
                    } else {
                        // We're outside of a tag somewhere
                        $wordBuffer .= $char;
                    }

                    break;
            }
        }

        // Finish flushing the word buffer
        if ($wordBuffer !== "") {
            $tokens[] = new Token(
                TokenTypes::T_WORD,
                $wordBuffer,
                $textLength - mb_strlen($wordBuffer)
            );
        }

        $tokens[] = new Token(TokenTypes::T_EOF, null, $textLength);

        return $tokens;
    }

    /**
     * Gets text around a certain position for use in exceptions
     *
     * @param array $charArray The char array
     * @param int $position The numerical position to grab text around
     * @return string The surrounding text
     */
    private function getSurroundingText(array $charArray, int $position) : string
    {
        if (count($charArray) <= 3) {
            return implode("", $charArray);
        }

        if ($position <= 3) {
            return implode("", array_slice($charArray, 0, 4));
        }

        return implode("", array_slice($charArray, $position - 3, 4));
    }

    /**
     * Looks back at the previous character in the string
     *
     * @param array $charArray The char array
     * @param int $currPosition The current position
     * @return string|null The previous character if there is one, otherwise null
     */
    private function lookBehind(array $charArray, int $currPosition)
    {
        if ($currPosition === 0 || count($charArray) === 0) {
            return null;
        }

        return $charArray[$currPosition - 1];
    }

    /**
     * Peeks at the next character in the string
     *
     * @param array $charArray The char array
     * @param int $currPosition The current position
     * @return string|null The next character if there is one, otherwise null
     */
    private function peek(array $charArray, int $currPosition)
    {
        $charArrayLength = count($charArray);

        if ($charArrayLength === 0 || $charArrayLength === $currPosition + 1) {
            return null;
        }

        return $charArray[$currPosition + 1];
    }
}
