<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Mocks a command with arguments and options
 */
namespace RDev\Tests\Console\Commands\Mocks;
use RDev\Console\Commands;
use RDev\Console\Requests;
use RDev\Console\Responses;

class HappyHolidayCommand extends Commands\Command
{
    public function __construct()
    {
        parent::__construct("holiday", "Wishes someone a happy holiday");
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Responses\IResponse $response)
    {
        $message = "Happy " . $this->getArgumentValue("holiday");

        if($this->getOptionValue("yell") == "yes")
        {
            $message .= "!";
        }

        $response->write($message);
    }

    /**
     * {@inheritdoc}
     */
    protected function setArgumentsAndOptions()
    {
        $this->addArgument(new Requests\Argument(
            "holiday",
            Requests\ArgumentTypes::REQUIRED,
            "Holiday to wish someone"
        ));
        $this->addOption(new Requests\Option(
            "yell",
            "y",
            Requests\OptionTypes::OPTIONAL_VALUE,
            "Whether or not we yell",
            "yes"
        ));
    }
}