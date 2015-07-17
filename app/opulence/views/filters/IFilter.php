<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for view filters to implement
 */
namespace Opulence\Views\Filters;

interface IFilter
{
    /**
     * Filters input for use in a view
     *
     * @param string $input The input to filter
     * @param array $options The list of options to use to filter the input
     * @return string The filtered input
     */
    public function run($input, array $options = []);
}