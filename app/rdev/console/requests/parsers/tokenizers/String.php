<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the string tokenizer
 */
namespace RDev\Console\Requests\Parsers\Tokenizers;

class String implements ITokenizer
{
    /**
     * {@inheritdoc}
     */
    public function tokenize($input)
    {
        $input = trim($input);
        $inDoubleQuotes = false;
        $inSingleQuotes = false;
        $inputLength = strlen($input);
        $previousChar = "";
        $buffer = "";
        $tokens = [];

        for($charIter = 0;$charIter < $inputLength;$charIter++)
        {
            $char = $input[$charIter];

            switch($char)
            {
                case '"':
                    // If the double quote is inside single quotes, we treat it as part of a quoted string
                    if(!$inSingleQuotes)
                    {
                        $inDoubleQuotes = !$inDoubleQuotes;
                    }

                    $buffer .= '"';

                    break;
                case "'":
                    // If the single quote is inside double quotes, we treat it as part of a quoted string
                    if(!$inDoubleQuotes)
                    {
                        $inSingleQuotes = !$inSingleQuotes;
                    }

                    $buffer .= "'";

                    break;
                default:
                    if($inDoubleQuotes || $inSingleQuotes || $char != " ")
                    {
                        $buffer .= $char;
                    }
                    elseif($char == " " && $previousChar != " " && strlen($buffer) > 0)
                    {
                        // We've hit a space outside a quoted string, so flush the buffer
                        $tokens[] = $buffer;
                        $buffer = "";
                    }
            }

            $previousChar = $char;
        }

        // Flush out the buffer
        if(strlen($buffer) > 0)
        {
            $tokens[] = $buffer;
        }

        if($inDoubleQuotes || $inSingleQuotes)
        {
            throw new \RuntimeException("Unclosed " . ($inDoubleQuotes ? "double" : "single") . " quote");
        }

        return $tokens;
    }
}