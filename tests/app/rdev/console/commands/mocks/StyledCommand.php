<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a command with styled output
 */
namespace RDev\Tests\Console\Commands\Mocks;
use RDev\Console\Commands;
use RDev\Console\Requests;
use RDev\Console\Responses;

class StyledCommand extends Commands\Command
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
    protected function doExecute(Responses\IResponse $response)
    {
        $response->write("<b>I've got style</b>");
    }
}