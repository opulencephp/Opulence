<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a command with styled output
 */
namespace RDev\Tests\Console\Commands\Mocks;
use RDev\Console\Commands\Command;
use RDev\Console\Responses\IResponse;

class StyledCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        $this->setName("stylish");
        $this->setDescription("Shows an output with style");
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(IResponse $response)
    {
        $response->write("<b>I've got style</b>");
    }
}