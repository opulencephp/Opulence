<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the console response
 */
namespace RDev\Console\Responses;

class Console extends Response
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
    public function clear()
    {
        $this->write(chr(27) . "[2J" . chr(27) . "[;H");
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