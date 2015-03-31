<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a command without a name
 */
namespace RDev\Tests\Console\Commands\Mocks;
use RDev\Console\Commands\Command;
use RDev\Console\Responses\IResponse;

class NamelessCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        // Don't do anything
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(IResponse $response)
    {
        $response->write("foo");
    }
}