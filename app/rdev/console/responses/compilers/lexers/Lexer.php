<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the response lexer
 */
namespace RDev\Console\Responses\Compilers\Lexers;
use RDev\Console\Responses\Compilers\Tokens;

class Lexer implements ILexer
{
    /**
     * {@inheritdoc}
     */
    public function lex($text)
    {
        $tokens = [];
        $wordBuffer = "";
        $elementNameBuffer = "";
        $textLength = strlen($text);
        $inOpenTag = false;
        $inCloseTag = false;

        for($charIter = 0;$charIter < $textLength;$charIter++)
        {
            $char = $text[$charIter];

            switch($char)
            {
                case "<":
                    if($this->lookBehind($text, $charIter) == "\\")
                    {
                        // This tag was escaped
                        // Don't include the preceding slash
                        $wordBuffer = substr($wordBuffer, 0, -1) . $char;
                    }
                    else
                    {
                        // Check if this is a closing tag
                        if($this->peek($text, $charIter) == "/")
                        {
                            $inCloseTag = true;
                            $inOpenTag = false;
                        }
                        else
                        {
                            $inCloseTag = false;
                            $inOpenTag = true;
                        }

                        // Flush the word buffer
                        if($wordBuffer != "")
                        {
                            $tokens[] = new Tokens\Token(
                                Tokens\TokenTypes::T_WORD,
                                $wordBuffer,
                                $charIter - strlen($wordBuffer)
                            );
                            $wordBuffer = "";
                        }
                    }

                    break;
                case ">";
                    if($inOpenTag || $inCloseTag)
                    {
                        if($inOpenTag)
                        {
                            $tokens[] = new Tokens\Token(
                                Tokens\TokenTypes::T_TAG_OPEN,
                                $elementNameBuffer,
                                // Need to get the position of the beginning of the open tag
                                $charIter - strlen($elementNameBuffer) - 1
                            );
                        }
                        else
                        {
                            $tokens[] = new Tokens\Token(
                                Tokens\TokenTypes::T_TAG_CLOSE,
                                $elementNameBuffer,
                                // Need to get the position of the beginning of the close tag
                                $charIter - strlen($elementNameBuffer) - 2
                            );
                        }

                        $elementNameBuffer = "";
                        $inOpenTag = false;
                        $inCloseTag = false;
                    }
                    else
                    {
                        $wordBuffer .= $char;
                    }

                    break;
                default:
                    if($inOpenTag || $inCloseTag)
                    {
                        // We're in a tag, so buffer the element name
                        if($char != "/")
                        {
                            $elementNameBuffer .= $char;
                        }
                    }
                    else
                    {
                        // We're outside of a tag somewhere
                        $wordBuffer .= $char;
                    }

                    break;
            }
        }

        // Finish flushing the output buffer
        if($wordBuffer !== "")
        {
            $tokens[] = new Tokens\Token(
                Tokens\TokenTypes::T_WORD,
                $wordBuffer,
                $textLength - strlen($wordBuffer)
            );
        }

        $tokens[] = new Tokens\Token(Tokens\TokenTypes::T_EOF, null, $textLength);

        return $tokens;
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
        if(strlen($text) == 0 || $currPosition  == 0)
        {
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
        if(strlen($text) == 0 || strlen($text) == $currPosition + 1)
        {
            return null;
        }

        return $text[$currPosition + 1];
    }
}