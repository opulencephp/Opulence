<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Mocks a command with arguments and options
 */
namespace RDev\Tests\Console\Commands\Mocks;
use RDev\Console\Commands\Command;
use RDev\Console\Requests\Argument;
use RDev\Console\Requests\ArgumentTypes;
use RDev\Console\Requests\Option;
use RDev\Console\Requests\OptionTypes;
use RDev\Console\Responses\IResponse;

class HappyHolidayCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        $this->setName("holiday");
        $this->setDescription("Wishes someone a happy holiday");
        $this->addArgument(new Argument(
            "holiday",
            ArgumentTypes::REQUIRED,
            "Holiday to wish someone"
        ));
        $this->addOption(new Option(
            "yell",
            "y",
            OptionTypes::OPTIONAL_VALUE,
            "Whether or not we yell",
            "yes"
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(IResponse $response)
    {
        $message = "Happy " . $this->getArgumentValue("holiday");

        if($this->getOptionValue("yell") == "yes")
        {
            $message .= "!";
        }

        $response->write($message);
    }
}