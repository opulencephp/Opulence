<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the about command
 */
namespace RDev\Console\Commands;
use RDev\Console\Responses;

class About extends Command
{
    /** @var Commands The list of commands registered */
    private $commands = null;

    /**
     * @param Commands $commands The list of commands
     */
    public function __construct(Commands &$commands)
    {
        $this->commands = $commands;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Responses\IResponse $response)
    {
        $message = <<<EOF
About RDev Console
-----------------------
Commands:
EOF;

        $maxNameLength = 0;

        foreach($this->commands->getAll() as $command)
        {
            if(strlen($command->getName()) > $maxNameLength)
            {
                $maxNameLength = strlen(($command->getName()));
            }
        }

        foreach($this->commands->getAll() as $command)
        {
            $padding = str_repeat(" ", $maxNameLength - strlen($command->getName()));
            $message .= <<<EOF

   {$command->getName()}{$padding} - {$command->getDescription()}
EOF;
        }

        $response->writeln($message);
    }

    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        $this->setName("about")
            ->setDescription("Describes the RDev console application");
    }
}