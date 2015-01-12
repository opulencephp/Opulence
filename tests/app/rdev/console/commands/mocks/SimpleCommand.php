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
     * @param string $name The name of the command
     * @param string $description A brief description of the command
     * @param string $helpText The help text of the command
     */
    public function __construct($name, $description, $helpText = "")
    {
        $this->setName($name);
        $this->setDescription($description);
        $this->setHelpText($helpText);

        parent::__construct();
    }

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