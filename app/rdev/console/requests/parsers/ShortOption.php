<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the short option parser
 */
namespace RDev\Console\Requests\Parsers;

class ShortOption
{
    /**
     * Parses a short option token and returns an array of data
     *
     * @param string $token The token to parse
     * @return array The name of the option mapped to its value
     * @throws \RuntimeException Thrown if the option could not be parsed
     */
    public function parse($token)
    {
        if(substr($token, 0, 1) !== "-")
        {
            throw new \RuntimeException("Invalid short option \"$token\"");
        }

        // Trim the "-"
        $token = substr($token, 1);

        $options = [];

        // Each character in a short option is an option
        for($charIter = 0;$charIter < strlen($token);$charIter++)
        {
            $options[] = [$token[$charIter], null];
        }

        return $options;
    }
}