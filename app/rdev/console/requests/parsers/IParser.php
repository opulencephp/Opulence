<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for request parsers to implement
 */
namespace RDev\Console\Requests\Parsers;
use RDev\Console\Requests;

interface IParser
{
    /**
     * Parses raw input into a request
     *
     * @param mixed $input The input to parse
     * @return Requests\IRequest The parsed request
     * @throws \InvalidArgumentException Thrown if the input was not of the type the parser was expecting
     * @throws \RuntimeException Thrown if the input could not be parsed
     */
    public function parse($input);
}