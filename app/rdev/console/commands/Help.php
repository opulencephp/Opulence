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
Command: {{name}}
-----------------------
{{command}}

Description:
   {{description}}
Arguments:
{{arguments}}
Options:
{{options}}{{helpText}}
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
        $descriptionText = "No description";
        $helpText = "";

        if($this->command->getDescription() != "")
        {
            $descriptionText = $this->command->getDescription();
        }

        if($this->command->getHelpText() != "")
        {
            $helpText = PHP_EOL . "Help:" . PHP_EOL . "   " . $this->command->getHelpText();
        }

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
        $compiledTemplate = str_replace("{{command}}", $this->getCommandText($this->command), $compiledTemplate);
        $compiledTemplate = str_replace("{{description}}", $descriptionText, $compiledTemplate);
        $compiledTemplate = str_replace("{{name}}", $this->command->getName(), $compiledTemplate);
        $compiledTemplate = str_replace("{{arguments}}", $argumentText, $compiledTemplate);
        $compiledTemplate = str_replace("{{options}}", $optionText, $compiledTemplate);
        $compiledTemplate = str_replace("{{helpText}}", $helpText, $compiledTemplate);

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
     * Gets the command as text
     *
     * @param ICommand $command The command to convert
     * @return string The command as text
     */
    private function getCommandText(ICommand $command)
    {
        $text = $command->getName() . " ";

        // Output the options
        foreach($command->getOptions() as $option)
        {
            $text .= "[--{$option->getName()}";

            if($option->getShortName() !== null)
            {
                $text .= "|-{$option->getShortName()}";
            }

            $text .= "] ";
        }

        /** @var Requests\Argument[] $requiredArguments */
        $requiredArguments = [];
        /** @var Requests\Argument[] $optionalArguments */
        $optionalArguments = [];
        /** @var Requests\Argument $arrayArgument */
        $arrayArgument = null;

        // Categorize each argument
        foreach($command->getArguments() as $argument)
        {
            if($argument->isRequired() && !$argument->isArray())
            {
                $requiredArguments[] = $argument;
            }
            elseif($argument->isOptional() && !$argument->isArray())
            {
                $optionalArguments[] = $argument;
            }

            if($argument->isArray())
            {
                $arrayArgument = $argument;
            }
        }

        // Output the required arguments
        foreach($requiredArguments as $argument)
        {
            $text .= $argument->getName() . " ";
        }

        // Output the optional arguments
        foreach($optionalArguments as $argument)
        {
            $text .= "[{$argument->getName()}] ";
        }

        // Output the array argument
        if($arrayArgument !== null)
        {
            $arrayArgumentTextOne = $arrayArgument->getName() . "1";
            $arrayArgumentTextN = $arrayArgument->getName() . "N";

            if($arrayArgument->isOptional())
            {
                $arrayArgumentTextOne = "[$arrayArgumentTextOne]";
                $arrayArgumentTextN = "[$arrayArgumentTextN]";
            }

            $text .= "$arrayArgumentTextOne...$arrayArgumentTextN";
        }

        return trim($text);
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