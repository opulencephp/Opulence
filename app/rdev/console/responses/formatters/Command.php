<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Formats a command into a text representation
 */
namespace RDev\Console\Responses\Formatters;
use RDev\Console\Commands;
use RDev\Console\Requests;

class Command
{
    /**
     * Gets the command as text
     *
     * @param Commands\ICommand $command The command to convert
     * @return string The command as text
     */
    public function format(Commands\ICommand $command)
    {
        $text = $command->getName() . " ";

        // Output the options
        foreach($command->getOptions() as $option)
        {
            $text .= $this->formatOption($option) . " ";
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
            $text .= $this->formatArrayArgument($arrayArgument);
        }

        return trim($text);
    }

    /**
     * Formats an array argument
     *
     * @param Requests\Argument $argument The argument to format
     * @return string The formatted array argument
     */
    private function formatArrayArgument(Requests\Argument $argument)
    {
        $arrayArgumentTextOne = $argument->getName() . "1";
        $arrayArgumentTextN = $argument->getName() . "N";

        if($argument->isOptional())
        {
            $arrayArgumentTextOne = "[$arrayArgumentTextOne]";
            $arrayArgumentTextN = "[$arrayArgumentTextN]";
        }

        return "$arrayArgumentTextOne...$arrayArgumentTextN";
    }

    /**
     * Formats an option
     *
     * @param Requests\Option $option The option to format
     * @return string The formatted option
     */
    private function formatOption(Requests\Option $option)
    {
        $text = "[--{$option->getName()}";

        if($option->valueIsOptional())
        {
            $text .= "=" . $option->getDefaultValue();
        }

        if($option->getShortName() !== null)
        {
            $text .= "|-{$option->getShortName()}";
        }

        $text .= "]";

        return $text;
    }
}