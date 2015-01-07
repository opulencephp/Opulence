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
    /** @var string The template for the output */
    private static $template = <<<EOF
-----------------------
Command: {{command}}
-----------------------
Arguments:
{{arguments}}
Options:
{{options}}
EOF;
    /** @var ICommand The command to help with */
    private $command = null;

    /**
     * {@inheritdoc}
     */
    public function execute(Responses\IResponse $response)
    {
        $argumentText = "";
        $optionText = "";

        foreach($this->command->getArguments() as $argument)
        {
            $argumentText .= $this->getArgumentText($argument);
        }

        foreach($this->command->getOptions() as $option)
        {
            $optionText .= $this->getOptionText($option);
        }

        // Trim excess new lines
        $argumentText = trim($argumentText, PHP_EOL);
        $optionText = trim($optionText, PHP_EOL);

        // Compile the template
        $compiledTemplate = self::$template;
        $compiledTemplate = str_replace("{{command}}", $this->command->getName(), $compiledTemplate);
        $compiledTemplate = str_replace("{{arguments}}", $argumentText, $compiledTemplate);
        $compiledTemplate = str_replace("{{options}}", $optionText, $compiledTemplate);

        $response->writeln($compiledTemplate);
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

    /**
     * Converts an argument to text
     *
     * @param Requests\Argument $argument The argument to convert to text
     * @return string The argument as text
     */
    private function getArgumentText(Requests\Argument $argument)
    {
        return "   {$argument->getName()} - {$argument->getDescription()}" . PHP_EOL;
    }

    /**
     * Converts an option to text
     *
     * @param Requests\Option $option The option to convert to text
     * @return string The option as text
     */
    private function getOptionText(Requests\Option $option)
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

        return "   $optionNames - {$option->getDescription()}" . PHP_EOL;
    }
}