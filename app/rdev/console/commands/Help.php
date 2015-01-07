<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the help command
 */
namespace RDev\Console\Commands;
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
            $response->writeln(<<<EOF
   {$option->getName()} - {$option->getDescription()}
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
            ->setDescription("Displays information about a command");
    }
}