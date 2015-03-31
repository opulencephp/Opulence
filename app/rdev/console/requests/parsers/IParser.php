<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for request parsers to implement
 */
namespace RDev\Console\Requests\Parsers;
use InvalidArgumentException;
use RuntimeException;
use RDev\Console\Requests\IRequest;

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