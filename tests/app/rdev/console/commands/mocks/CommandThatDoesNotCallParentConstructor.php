<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Mocks a command that does not call the parent constructor
 */
namespace RDev\Tests\Console\Commands\Mocks;
use RDev\Console\Commands;
use RDev\Console\Requests;
use RDev\Console\Responses;

class CommandThatDoesNotCallParentConstructor extends Commands\Command
{
    public function __construct()
    {
        // Don't do anything
    }

    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        $this->setName("holiday");
        $this->setDescription("Wishes someone a happy holiday");
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

    /**
     * {@inheritdoc}
     */
    protected function doExecute(Responses\IResponse $response)
    {
        $message = "Happy " . $this->getArgumentValue("holiday");

        if($this->getOptionValue("yell") == "yes")
        {
            $message .= "!";
        }

        $response->write($message);
    }
}