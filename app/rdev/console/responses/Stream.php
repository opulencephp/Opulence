<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the stream response
 */
namespace RDev\Console\Responses;

class Stream extends Response
{
    /** @var resource The output stream */
    protected $stream = null;

    /**
     * @param resource $stream The stream to write to
     * @param Compilers\ICompiler $compiler The response compiler to use
     * @throws \InvalidArgumentException Thrown if the stream is not a resource
     */
    public function __construct($stream, Compilers\ICompiler $compiler)
    {
        if(!is_resource($stream))
        {
            throw new \InvalidArgumentException("The stream must be a resource");
        }

        parent::__construct($compiler);

        $this->stream = $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        // Don't do anything
    }

    /**
     * @return resource
     */
    public function getStream()
    {
        return $this->stream;
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