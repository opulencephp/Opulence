<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for console responses to implement
 */
namespace RDev\Console\Responses;

interface IResponse
{
    /**
     * Clears the response from view
     */
    public function clear();

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