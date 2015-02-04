<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the silent response, which does not write anything
 */
namespace RDev\Console\Responses;

class Silent extends Response
{
    public function __construct()
    {
        // Don't do anything
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        // Don't do anything
    }

    /**
     * {@inheritdoc}
     */
    public function write($messages)
    {
        // Don't do anything
    }

    /**
     * {@inheritdoc}
     */
    public function writeln($messages)
    {
        // Don't do anything
    }

    /**
     * {@inheritdoc}
     */
    protected function doWrite($message, $includeNewLine)
    {
        // Don't do anything
    }
}