<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for console commands to implement
 */
namespace RDev\Console\Commands;
use RDev\Console\Input;
use RDev\Console\Output;

interface ICommand 
{
    /**
     * Executes the command
     *
     * @param Input\IInput $input The console input
     * @param Output\IOutput $output The console output to write to
     * @return int|null Null or the status code of the command
     */
    public function execute(Input\IInput $input, Output\IOutput $output);
}