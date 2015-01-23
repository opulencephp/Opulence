<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for console responses to implement
 */
namespace RDev\Console\Responses;
use RDev\Console\Responses\Formatters\Elements;

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
     * @throws \RuntimeException Thrown if there was an issue writing the messages
     */
    public function write($messages);

    /**
     * Writes to output with a newline character at the end
     *
     * @param string|array $messages The message or messages to display
     * @throws \RuntimeException Thrown if there was an issue writing the messages
     */
    public function writeln($messages);
}