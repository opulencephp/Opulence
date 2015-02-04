<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Mocks a simple command for use in testing
 */
namespace RDev\Tests\Console\Commands\Mocks;
use RDev\Console\Commands;
use RDev\Console\Requests;
use RDev\Console\Responses;

class SimpleCommand extends Commands\Command
{
    /**
     * @param Commands\Commands $commands The list of registered commands
     * @param string $name The name of the command
     * @param string $description A brief description of the command
     * @param string $helpText The help text of the command
     */
    public function __construct(Commands\Commands $commands, $name, $description, $helpText = "")
    {
        $this->setName($name);
        $this->setDescription($description);
        $this->setHelpText($helpText);

        parent::__construct($commands);
    }

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
    protected function doExecute(Responses\IResponse $response)
    {
        $response->write("foo");
    }
}