<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the argument parser
 */
namespace RDev\Console\Requests\Parsers;

class Argument
{
    /**
     * Parses an argument value
     *
     * @param string $token The token to parse
     * @return string The parsed argument
     */
    public function parse($token)
    {
        return $this->trimQuotes($token);
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