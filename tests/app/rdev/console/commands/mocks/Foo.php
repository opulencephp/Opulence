<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Mocks a command for use in testing
 */
namespace RDev\Tests\Console\Commands\Mocks;
use RDev\Console\Commands;
use RDev\Console\Input;
use RDev\Console\Output;

class Foo implements Commands\ICommand
{
    /**
     * {@inheritdoc}
     */
    public function execute(Input\IInput $input, Output\IOutput $output)
    {
        $output->write("foo");
    }
}