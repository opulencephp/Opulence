<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a command that returns a different status code depending on the options
 */
namespace Opulence\Tests\Console\Commands\Mocks;
use Opulence\Console\Commands\Command;
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Responses\IResponse;
use Opulence\Framework\Console\StatusCodes;

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