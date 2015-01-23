<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a basic response
 */
namespace RDev\Console\Responses;
use RDev\Console\Responses\Formatters\Elements;

abstract class Response implements IResponse
{
    /** @var Compilers\ICompiler The response compiler to use */
    protected $compiler = null;

    /**
     * @param Compilers\ICompiler $compiler The response compiler to use
     */
    public function __construct(Compilers\ICompiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * {@inheritdoc}
     */
    public function write($messages)
    {
        if(!is_array($messages))
        {
            $messages = [$messages];
        }

        foreach($messages as $message)
        {
            $this->doWrite($this->compiler->compile($message), false);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function writeln($messages)
    {
        if(!is_array($messages))
        {
            $messages = [$messages];
        }

        foreach($messages as $message)
        {
            $this->doWrite($this->compiler->compile($message), true);
        }
    }

    /**
     * Actually performs the writing
     *
     * @param string $message The message to write
     * @param bool $includeNewLine True if we are to include a new line character at the end of the message
     */
    abstract protected function doWrite($message, $includeNewLine);
}