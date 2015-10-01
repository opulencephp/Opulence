<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a command that does not call the parent constructor
 */
namespace Opulence\Tests\Console\Commands\Mocks;

use Opulence\Console\Commands\Command;
use Opulence\Console\Requests\Argument;
use Opulence\Console\Requests\ArgumentTypes;
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Responses\IResponse;

class CommandThatDoesNotCallParentConstructor extends Command
{
    public function __construct()
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
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