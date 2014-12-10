<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a filter meant to prevent cross-site scripting
 */
namespace RDev\Views\Filters;

class XSS implements IFilter
{
    /**
     * Filters the input parameter for XSS attacks
     *
     * {@inheritdoc}
     * @param array $options The list of options with the following keys:
     *      "forURL" => true if the input is being filtered for use in a URL, otherwise false
     */
    public function run($input, array $options = [])
    {
        $filteredInput = $input;

        if(isset($options["forURL"]) && $options["forURL"])
        {
            // For URLs, "%27" is the correct way to display an apostrophe
            // For HTML, "#39;" (conversion is done in functions below) is the correct way to display an apostrophe
            $filteredInput = str_replace("'", "%27", $input);
        }

        $filteredInput = mb_convert_encoding($filteredInput, "UTF-8", "UTF-8");
        $filteredInput = filter_var($filteredInput, \FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        return $filteredInput;
    }
} 