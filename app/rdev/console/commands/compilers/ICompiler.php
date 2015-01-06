<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for command compilers to implement
 */
namespace RDev\Console\Commands\Compilers;
use RDev\Console\Commands;
use RDev\Console\Requests;

interface ICompiler 
{
    /**
     * Compiles a command using request data
     *
     * @param Commands\ICommand $command The command to compile
     * @param Requests\IRequest $request The request from the user
     * @return Commands\Command The compiled command
     * @throws \RuntimeException Thrown if there was an error compiling the command
     */
    public function compile(Commands\ICommand $command, Requests\IRequest $request);
}