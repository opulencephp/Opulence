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
    /** @var string The template for the output */
    private static $template = <<<EOF
-----------------------
About RDev Console
-----------------------
Commands:
{{commands}}
EOF;
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
        // Compile the template
        $commandText = $this->getCommandText();
        $compiledTemplate = self::$template;
        $compiledTemplate = str_replace("{{commands}}", $commandText, $compiledTemplate);

        $response->writeln($compiledTemplate);
    }

    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        $this->setName("about")
            ->setDescription("Describes the RDev console application");
    }

    /**
     * Converts commands to text
     *
     * @return string The commands as text
     */
    private function getCommandText()
    {
        $text = "";
        $maxNameLength = 0;

        // Figure out the longest command name
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
            $text .= "   {$command->getName()}$padding - {$command->getDescription()}" . PHP_EOL;
        }

        $text = trim($text, PHP_EOL);

        return $text;
    }
}