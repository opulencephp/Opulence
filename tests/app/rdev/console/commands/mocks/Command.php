<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Mocks a command for use in testing
 */
namespace RDev\Tests\Console\Commands\Mocks;
use RDev\Console\Commands;
use RDev\Console\Requests;
use RDev\Console\Responses;

class Command extends Commands\Command
{
    /** @var Requests\Argument An argument that will be set in the configuration of this command */
    private $argumentSetInMock = null;

    /**
     * {@inheritdoc}
     */
    public function execute(Responses\IResponse $response)
    {
        $response->write("foo");
    }

    /**
     * @return Requests\Argument
     */
    public function getArgumentSetInMock()
    {
        return $this->argumentSetInMock;
    }

    /**
     * {@inheritdoc}
     */
    protected function setArgumentsAndOptions()
    {
        $this->argumentSetInMock = new Requests\Argument("argumentSetInMock", Requests\ArgumentTypes::REQUIRED, "Blah");
    }
}