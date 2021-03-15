<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Console\Responses\Formatters;

use Opulence\Console\Commands\ICommand;
use Opulence\Console\Requests\Argument;
use Opulence\Console\Requests\Option;

/**
 * Formats a command into a text representation
 */
class CommandFormatter
{
    /**
     * Gets the command as text
     *
     * @param ICommand $command The command to convert
     * @return string The command as text
     */
    public function format(ICommand $command) : string
    {
        $text = $command->getName() . ' ';

        // Output the options
        foreach ($command->getOptions() as $option) {
            $text .= $this->formatOption($option) . ' ';
        }

        /** @var Argument[] $requiredArguments */
        $requiredArguments = [];
        /** @var Argument[] $optionalArguments */
        $optionalArguments = [];
        /** @var Argument $arrayArgument */
        $arrayArgument = null;

        // Categorize each argument
        foreach ($command->getArguments() as $argument) {
            if ($argument->isRequired() && !$argument->isArray()) {
                $requiredArguments[] = $argument;
            } elseif ($argument->isOptional() && !$argument->isArray()) {
                $optionalArguments[] = $argument;
            }

            if ($argument->isArray()) {
                $arrayArgument = $argument;
            }
        }

        // Output the required arguments
        foreach ($requiredArguments as $argument) {
            $text .= $argument->getName() . ' ';
        }

        // Output the optional arguments
        foreach ($optionalArguments as $argument) {
            $text .= "[{$argument->getName()}] ";
        }

        // Output the array argument
        if ($arrayArgument !== null) {
            $text .= $this->formatArrayArgument($arrayArgument);
        }

        return trim($text);
    }

    /**
     * Formats an array argument
     *
     * @param Argument $argument The argument to format
     * @return string The formatted array argument
     */
    private function formatArrayArgument(Argument $argument) : string
    {
        $arrayArgumentTextOne = $argument->getName() . '1';
        $arrayArgumentTextN = $argument->getName() . 'N';

        if ($argument->isOptional()) {
            $arrayArgumentTextOne = "[$arrayArgumentTextOne]";
            $arrayArgumentTextN = "[$arrayArgumentTextN]";
        }

        return "$arrayArgumentTextOne...$arrayArgumentTextN";
    }

    /**
     * Formats an option
     *
     * @param Option $option The option to format
     * @return string The formatted option
     */
    private function formatOption(Option $option) : string
    {
        $text = "[--{$option->getName()}";

        if ($option->valueIsOptional()) {
            $text .= '=' . $option->getDefaultValue();
        }

        if ($option->getShortName() !== null) {
            $text .= "|-{$option->getShortName()}";
        }

        $text .= ']';

        return $text;
    }
}
