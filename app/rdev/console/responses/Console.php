<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the console response
 */
namespace RDev\Console\Responses;

class Console extends Stream
{
    /**
     * @param Compilers\ICompiler $compiler The response compiler to use
     */
    public function __construct(Compilers\ICompiler $compiler)
    {
        parent::__construct(fopen("php://stdout", "w"), $compiler);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->write(chr(27) . "[2J" . chr(27) . "[;H");
    }
}