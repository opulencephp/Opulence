<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the long option parser
 */
namespace RDev\Console\Requests\Parsers;

class LongOption
{
    /**
     * Parses a long option token and returns an array of data
     *
     * @param string $token The token to parse
     * @param array $remainingTokens The list of remaining tokens
     * @return array The name of the option mapped to its value
     * @throws \RuntimeException Thrown if the option could not be parsed
     */
    public function parse($token, array &$remainingTokens)
    {
        if(substr($token, 0, 2) !== "--")
        {
            throw new \RuntimeException("Invalid long option \"$token\"");
        }

        // Trim the "--"
        $token = substr($token, 2);

        if(strpos($token, "=") === false)
        {
            // It's of the form "--foo bar"
            $nextToken = array_shift($remainingTokens);
            // Make it "--foo=bar"
            $token .= "=" . $nextToken;
        }

        list($name, $value) = explode("=", $token);
        $value = $this->trimQuotes($value);

        return [$name, $value];
    }

    /**
     * Trims the outer-most quotes from a token
     *
     * @param string $token Trims quotes off of a token
     * @return string The trimmed token
     */
    private function trimQuotes($token)
    {
        // Trim any quotes
        if(($firstValueChar = substr($token, 0, 1)) == substr($token, -1))
        {
            if($firstValueChar == "'")
            {
                $token = trim($token, "'");
            }
            elseif($firstValueChar == '"')
            {
                $token = trim($token, '"');
            }
        }

        return $token;
    }
}