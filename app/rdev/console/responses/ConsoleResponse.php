<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the console response
 */
namespace RDev\Console\Responses;
use RDev\Console\Responses\Compilers\ICompiler;

class ConsoleResponse extends StreamResponse
{
    /**
     * @param ICompiler $compiler The response compiler to use
     */
    public function __construct(ICompiler $compiler)
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