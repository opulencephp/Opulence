<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines tools meant to prevent cross-site scripting
 */
namespace RDev\Views\Security;

class XSS
{
    /**
     * Filters the input parameter for XSS attacks
     *
     * @param string $input The input string to be filtered
     * @param bool $forURL Whether or not we're trying to escape for a string to be output in the URL
     * @return string The filtered input
     */
    public static function filter($input, $forURL = false)
    {
        // For URLs, "%27" is the correct way to display an apostrophe
        // For HTML, "#39;" (conversion is done in functions below) is the correct way to display an apostrophe
        $filteredInput = $forURL ? str_replace("'", "%27", $input) : $input;
        $filteredInput = mb_convert_encoding($filteredInput, "UTF-8", "UTF-8");
        $filteredInput = filter_var($filteredInput, \FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        return $filteredInput;
    }
} 