<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the console output
 */
namespace RDev\Console\Output;

class Console extends Output
{
    /** @var mixed The output stream */
    private $stream = null;

    public function __construct()
    {
        $this->stream = fopen("php://stdout", "w");
    }

    /**
     * {@inheritdoc}
     */
    protected function doWrite($message, $includeNewLine)
    {
        fwrite($this->stream, $message . ($includeNewLine ? PHP_EOL : ""));
        fflush($this->stream);
    }
}