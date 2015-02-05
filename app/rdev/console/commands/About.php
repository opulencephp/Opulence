<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the about command
 */
namespace RDev\Console\Commands;
use RDev\Console\Responses;
use RDev\Console\Responses\Formatters;

class About extends Command
{
    /** @var string The template for the output */
    private static $template = <<<EOF
-----------------------------
About <b>RDev Console</b> {{version}}
-----------------------------
<comment>Commands:</comment>
{{commands}}
EOF;
    /** @var Formatters\Padding The space padding formatter to use */
    private $paddingFormatter  = null;
    /** @var string The version number of the application */
    private $applicationVersion = "Unknown";

    /**
     * @param Commands $commands The list of commands
     * @param Formatters\Padding $paddingFormatter The space padding formatter to use
     * @param string $applicationVersion The version number of the application
     */
    public function __construct(Commands &$commands, Formatters\Padding $paddingFormatter, $applicationVersion)
    {
        parent::__construct();

        $this->setCommands($commands);
        $this->paddingFormatter = $paddingFormatter;
        $this->applicationVersion = $applicationVersion;
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
     * {@inheritdoc}
     */
    protected function doExecute(Responses\IResponse $response)
    {
        // Compile the template
        $commandText = $this->getCommandText();
        $compiledTemplate = self::$template;
        $compiledTemplate = str_replace("{{commands}}", $commandText, $compiledTemplate);
        $compiledTemplate = str_replace("{{version}}", $this->applicationVersion, $compiledTemplate);

        $response->writeln($compiledTemplate);
    }

    /**
     * Converts commands to text
     *
     * @return string The commands as text
     */
    private function getCommandText()
    {
        if(count($this->commands->getAll()) == 0)
        {
            return " <info>No commands</info>";
        }

        /**
         * Sorts the commands by name
         *
         * @param ICommand $a
         * @param ICommand $b
         * @return int The result of the comparison
         */
        $sort = function($a, $b)
        {
            return $a->getName() < $b->getName() ? -1 : 1;
        };

        $commands = $this->commands->getAll();
        usort($commands, $sort);
        $commandTexts = [];

        // Figure out the longest command name
        foreach($commands as $command)
        {
            $commandTexts[] = [$command->getName(), $command->getDescription()];
        }

        return $this->paddingFormatter->format($commandTexts, function($line)
        {
            return "  <info>{$line[0]}</info> - {$line[1]}";
        });
    }
}