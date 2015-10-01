<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for request parsers to implement
 */
namespace Opulence\Console\Requests\Parsers;

use InvalidArgumentException;
use Opulence\Console\Requests\IRequest;
use RuntimeException;

interface IParser
{
    /**
     * Parses raw input into a request
     *
     * @param mixed $input The input to parse
     * @return IRequest The parsed request
     * @throws InvalidArgumentException Thrown if the input was not of the type the parser was expecting
     * @throws RuntimeException Thrown if the input could not be parsed
     */
    public function parse($input);
}