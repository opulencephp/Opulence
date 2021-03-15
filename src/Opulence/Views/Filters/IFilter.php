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
 * Defines the interface for view filters to implement
 */
interface IFilter
{
    /**
     * Filters input for use in a view
     *
     * @param string $input The input to filter
     * @param array $options The list of options to use to filter the input
     * @return string The filtered input
     */
    public function run(string $input, array $options = []) : string;
}
