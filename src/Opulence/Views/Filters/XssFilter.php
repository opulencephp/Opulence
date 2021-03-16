<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Views\Filters;

/**
 * Defines a filter meant to prevent cross-site scripting
 */
class XssFilter implements IFilter
{
    /**
     * Filters the input parameter for XSS attacks
     *
     * @inheritdoc
     * @param array $options The list of options with the following keys:
     *      "forURL" => true if the input is being filtered for use in a URL, otherwise false
     */
    public function run(string $input, array $options = []) : string
    {
        $filteredInput = $input;

        if (isset($options['forUrl']) && $options['forUrl']) {
            // For URLs, "%27" is the correct way to display an apostrophe
            // For HTML, "#39;" (conversion is done in functions below) is the correct way to display an apostrophe
            $filteredInput = str_replace("'", '%27', $input);
        }

        $filteredInput = filter_var($filteredInput, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        return $filteredInput;
    }
}
