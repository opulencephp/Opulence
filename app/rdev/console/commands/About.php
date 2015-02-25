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
        $compiledTemplate = self::$template;
        $compiledTemplate = str_replace("{{commands}}", $this->getCommandText(), $compiledTemplate);
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
            return "  <info>No commands</info>";
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
        $firstCommandNamesToCategories = [];

        // Figure out the longest command name
        foreach($commands as $command)
        {
            $commandNameParts = explode(":", $command->getName());

            // If this command belongs to a category
            if(count($commandNameParts) > 1 && !in_array($commandNameParts[0], $firstCommandNamesToCategories))
            {
                $firstCommandNamesToCategories[$command->getName()] = $commandNameParts[0];
            }

            $commandTexts[] = [$command->getName(), $command->getDescription()];
        }

        return $this->paddingFormatter->format($commandTexts, function($row) use ($firstCommandNamesToCategories)
        {
            $output = "";
            $commandNameParts = explode(":", $row[0]);

            // If this is the first command of its category, display the category
            if(count($commandNameParts) > 1 && isset($firstCommandNamesToCategories[trim($row[0])]))
            {
                $output .= "<comment>{$firstCommandNamesToCategories[trim($row[0])]}</comment>" . PHP_EOL;
            }

            return $output . "  <info>{$row[0]}</info> - {$row[1]}";
        });
    }
}