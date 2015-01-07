<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the help command
 */
namespace RDev\Console\Commands;
use RDev\Console\Requests;
use RDev\Console\Responses;

class Help extends Command
{
    /** @var ICommand The command to help with */
    private $command = null;

    /**
     * {@inheritdoc}
     */
    public function execute(Responses\IResponse $response)
    {
        $response->writeln(<<<EOF
-----------------------
Command: {$this->command->getName()}
-----------------------
Arguments:
EOF
        );

        foreach($this->command->getArguments() as $argument)
        {
            $response->writeln(<<<EOF
   {$argument->getName()} - {$argument->getDescription()}
EOF
            );
        }

        $response->writeln("Options:");

        foreach($this->command->getOptions() as $option)
        {
            $optionNames = "--{$option->getName()}";

            if($option->valueIsOptional())
            {
                $optionNames .= "[={$option->getDefaultValue()}]";
            }

            if($option->getShortName() !== null)
            {
                $optionNames .= "|-{$option->getShortName()}";
            }

            $response->writeln(<<<EOF
   {$optionNames} - {$option->getDescription()}
EOF
            );
        }
    }

    /**
     * Sets the command to help with
     *
     * @param ICommand $command The command to help with
     */
    public function setCommand(ICommand $command)
    {
        $this->command = $command;
    }

    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        $this->setName("help")
            ->setDescription("Displays information about a command")
            ->addArgument(new Requests\Argument(
                "command",
                Requests\ArgumentTypes::REQUIRED,
                "The command to get help with"
            ));
    }
}