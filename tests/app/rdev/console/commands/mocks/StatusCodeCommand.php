<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a command that returns a different status code depending on the options
 */
namespace RDev\Tests\Console\Commands\Mocks;
use RDev\Console\Commands;
use RDev\Console\Kernels;
use RDev\Console\Requests;
use RDev\Console\Responses;

class StatusCodeCommand extends Commands\Command
{
    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        $this->setName("statuscode");
        $this->setDescription("Returns a status code based on the options");
        $this->addOption(new Requests\Option(
            "code",
            "c",
            Requests\OptionTypes::REQUIRED_VALUE,
            "The status code to return",
            Kernels\StatusCodes::OK
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(Responses\IResponse $response)
    {
        return (int)$this->getOptionValue("code");
    }
}