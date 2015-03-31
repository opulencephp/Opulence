<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a command that returns a different status code depending on the options
 */
namespace RDev\Tests\Console\Commands\Mocks;
use RDev\Console\Commands\Command;
use RDev\Console\Kernels\StatusCodes;
use RDev\Console\Requests\Option;
use RDev\Console\Requests\OptionTypes;
use RDev\Console\Responses\IResponse;

class StatusCodeCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        $this->setName("statuscode");
        $this->setDescription("Returns a status code based on the options");
        $this->addOption(new Option(
            "code",
            "c",
            OptionTypes::REQUIRED_VALUE,
            "The status code to return",
            StatusCodes::OK
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(IResponse $response)
    {
        return (int)$this->getOptionValue("code");
    }
}