<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the string parser
 */
namespace RDev\Console\Requests\Parsers;
use RDev\Console\Requests;

class String implements IParser
{
    /**
     * {@inheritdoc}
     */
    public function parse($input)
    {
        $request = new Requests\Request();
        $tokens = $this->tokenize($input);
        $argumentCounter = 0;

        while($token = array_shift($tokens))
        {
            if(substr($token, 0, 2) == "--")
            {
                if(strpos($token, "=") === false)
                {
                    // It's of the form "--foo bar"
                    $nextToken = array_shift($tokens);
                    // Make it "--foo=bar"
                    $token .= "=" . $nextToken;
                }

                $option = $this->parseLongOption(substr($token, 2));
                $request->addOptionValue($option[0], $option[1]);
            }
            elseif(substr($token, 0, 1) == "-")
            {
                $options = $this->parseShortOption(substr($token, 1));

                foreach($options as $option)
                {
                    $request->addOptionValue($option[0], $option[1]);
                }
            }
            else
            {
                if($argumentCounter == 0)
                {
                    // We consider this to be the command name
                    $request->setCommandName($token);
                }
                else
                {
                    // We consider this to be an argument
                    $request->addArgumentValue($token);
                }

                $argumentCounter++;
            }
        }

        return $request;
    }

    /**
     * Parses a long option token and returns an array of data
     *
     * @param string $token The token to parse
     * @return array The name of the option mapped to its value
     * @throws \RuntimeException Thrown if the option could not be parsed
     */
    private function parseLongOption($token)
    {
        list($name, $value) = explode("=", $token);

        // Trim any quotes
        if(($firstValueChar = substr($value, 0, 1)) == substr($value, -1))
        {
            if($firstValueChar == "'")
            {
                $value = trim($value, "'");
            }
            elseif($firstValueChar == '"')
            {
                $value = trim($value, '"');
            }
        }

        return [$name, $value];
    }

    /**
     * Parses a short option token and returns an array of data
     *
     * @param string $token The token to parse
     * @return array The name of the option mapped to its value
     * @throws \RuntimeException Thrown if the option could not be parsed
     */
    private function parseShortOption($token)
    {
        $options = [];

        // Each character in a short option is an option
        for($charIter = 0;$charIter < strlen($token);$charIter++)
        {
            $options[] = [$token[$charIter], null];
        }

        return $options;
    }

    /**
     * Tokenizes a request string
     *
     * @param string $input The input to edit
     * @return array The list of tokens
     */
    private function tokenize($input)
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
                    $inDoubleQuotes = !$inDoubleQuotes;
                    $buffer .= '"';

                    break;
                case "'":
                    $inSingleQuotes = !$inSingleQuotes;
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
            throw new \RuntimeException("Unclosed " . ($inDoubleQuotes ? "double" : "single") . " quotes");
        }

        return $tokens;
    }
}