<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a command without a name
 */
namespace Opulence\Tests\Console\Commands\Mocks;
use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

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