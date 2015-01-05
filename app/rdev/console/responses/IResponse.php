<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for console outputs to implement
 */
namespace RDev\Console\Responses;

interface IResponse
{
    /**
     * Writes to output
     *
     * @param string|array $messages The message or messages to display
     */
    public function write($messages);

    /**
     * Writes to output with a newline character at the end
     *
     * @param string|array $messages The message or messages to display
     */
    public function writeln($messages);
}