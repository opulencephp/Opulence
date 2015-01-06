<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the argv parser
 */
namespace RDev\Console\Requests\Parsers;
use RDev\Console\Requests;

class Argv implements IParser
{
    /**
     * {@inheritdoc}
     */
    public function parse($input)
    {
        if($input === null)
        {
            $input = $_SERVER["argv"];
        }

        // Get rid of the application name
        array_shift($input);

        $tokens = explode(" ", $input);
        $arguments = [];
        $options = [];

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

                $options[] = $this->parseLongOption(substr($token, 0, 2));
            }
            elseif(substr($token, 0, 1) == "-")
            {
                $options = array_merge($options, $this->parseShortOption(substr($token, 2)));
            }
            else
            {
                $arguments[] = $token;
            }
        }
    }

    /**
     * Parses a long option token and returns an option
     *
     * @param string $token The token to parse
     * @return array The name of the option mapped to its value
     * @throws \RuntimeException Thrown if the option could not be parsed
     */
    private function parseLongOption($token)
    {
        list($name, $value) = explode("=", $token);

        // Trim any quotes
        if(($firstValueChar = substr($value, 0, 1)) == substr($value, -1, 1))
        {
            if($firstValueChar == "'")
            {
                $value = trim("'");
            }
            elseif($firstValueChar == '"')
            {
                $value = trim('"');
            }
        }

        return [$name => $value];
    }

    /**
     * Parses a short option token and returns an option
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
            $options[] = [$token[$charIter] => null];
        }

        return $options;
    }
}