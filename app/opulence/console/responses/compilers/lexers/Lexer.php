<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
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
    public function lex($text)
    {
        $tokens = [];
        $wordBuffer = "";
        $elementNameBuffer = "";
        $textLength = mb_strlen($text);
        $inOpenTag = false;
        $inCloseTag = false;

        for ($charIter = 0;$charIter < $textLength;$charIter++) {
            $char = $text[$charIter];

            switch ($char) {
                case "<":
                    if ($this->lookBehind($text, $charIter) == "\\") {
                        // This tag was escaped
                        // Don't include the preceding slash
                        $wordBuffer = mb_substr($wordBuffer, 0, -1) . $char;
                    } elseif ($inOpenTag || $inCloseTag) {
                        throw new RuntimeException(
                            sprintf(
                                "Invalid tags near \"%s\", character #%d",
                                $this->getSurroundingText($text, $charIter),
                                $charIter
                            )
                        );
                    } else {

                        // Check if this is a closing tag
                        if ($this->peek($text, $charIter) == "/") {
                            $inCloseTag = true;
                            $inOpenTag = false;
                        } else {
                            $inCloseTag = false;
                            $inOpenTag = true;
                        }

                        // Flush the word buffer
                        if ($wordBuffer != "") {
                            $tokens[] = new Token(
                                TokenTypes::T_WORD,
                                $wordBuffer,
                                $charIter - mb_strlen($wordBuffer)
                            );
                            $wordBuffer = "";
                        }
                    }

                    break;
                case ">";
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
                        if ($char != "/") {
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
     * @param string $text The full text
     * @param int $position The numerical position to grab text around
     * @return string The surrounding text
     */
    private function getSurroundingText($text, $position)
    {
        if (mb_strlen($text) <= 3) {
            return $text;
        }

        if ($position <= 3) {
            return mb_substr($text, 0, 4);
        }

        return mb_substr($text, $position - 3, 4);
    }

    /**
     * Looks back at the previous character in the string
     *
     * @param string $text The text to look behind in
     * @param int $currPosition The current position
     * @return string|null The previous character if there is one, otherwise null
     */
    private function lookBehind($text, $currPosition)
    {
        if (mb_strlen($text) == 0 || $currPosition == 0) {
            return null;
        }

        return $text[$currPosition - 1];
    }

    /**
     * Peeks at the next character in the string
     *
     * @param string $text The text to peek
     * @param int $currPosition The current position
     * @return string|null The next character if there is one, otherwise null
     */
    private function peek($text, $currPosition)
    {
        if (mb_strlen($text) == 0 || mb_strlen($text) == $currPosition + 1) {
            return null;
        }

        return $text[$currPosition + 1];
    }
}