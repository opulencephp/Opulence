<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a command without a name
 */
namespace RDev\Tests\Console\Commands\Mocks;
use RDev\Console\Commands;
use RDev\Console\Requests;
use RDev\Console\Responses;

class NamelessCommand extends Commands\Command
{
    /**
     * {@inheritdoc}
     */
    public function execute(Responses\IResponse $response)
    {
        $response->write("foo");
    }

    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        // Don't do anything
    }
}