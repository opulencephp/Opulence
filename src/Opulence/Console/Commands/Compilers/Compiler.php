<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Commands\Compilers;

use Opulence\Console\Commands\ICommand;
use Opulence\Console\Requests\IRequest;
use RuntimeException;

/**
 * Defines a command compiler
 */
class Compiler implements ICompiler
{
    /**
     * @inheritdoc
     */
    public function compile(ICommand $command, IRequest $request) : ICommand
    {
        $this->compileArguments($command, $request);
        $this->compileOptions($command, $request);

        return $command;
    }

    /**
     * Compiles arguments in a command
     *
     * @param ICommand $command The command to compile
     * @param IRequest $request The user request
     * @throws RuntimeException Thrown if there are too many arguments
     */
    protected function compileArguments(ICommand &$command, IRequest $request)
    {
        $argumentValues = $request->getArgumentValues();
        $commandArguments = $command->getArguments();

        if ($this->hasTooManyArguments($argumentValues, $commandArguments)) {
            throw new RuntimeException("Too many arguments");
        }

        $hasSetArrayArgument = false;

        foreach ($commandArguments as $argument) {
            if (count($argumentValues) == 0) {
                if (!$argument->isOptional()) {
                    throw new RuntimeException("Argument \"{$argument->getName()}\" does not have default value");
                }

                $command->setArgumentValue($argument->getName(), $argument->getDefaultValue());
            } else {
                if ($hasSetArrayArgument) {
                    throw new RuntimeException("Array argument must appear at end of list of arguments");
                }

                if ($argument->isArray()) {
                    // Add the rest of the values in the request to this argument
                    $restOfArgumentValues = [];

                    while (count($argumentValues) > 0) {
                        $restOfArgumentValues[] = array_shift($argumentValues);
                    }

                    $command->setArgumentValue($argument->getName(), $restOfArgumentValues);
                    $hasSetArrayArgument = true;
                } else {
                    $command->setArgumentValue($argument->getName(), array_shift($argumentValues));
                }
            }
        }
    }

    /**
     * Compiles options in a command
     *
     * @param ICommand $command The command to compile
     * @param IRequest $request The user request
     */
    protected function compileOptions(ICommand &$command, IRequest $request)
    {
        foreach ($command->getOptions() as $option) {
            $shortNameIsSet = $request->optionIsSet($option->getShortName());
            $longNameIsSet = $request->optionIsSet($option->getName());

            // All options are optional (duh)
            if ($shortNameIsSet || $longNameIsSet) {
                if ($longNameIsSet) {
                    $value = $request->getOptionValue($option->getName());
                } else {
                    $value = $request->getOptionValue($option->getShortName());
                }

                if (!$option->valueIsPermitted() && $value !== null) {
                    throw new RuntimeException("Option \"{$option->getName()}\" does not permit a value");
                }

                if ($option->valueIsRequired() && $value === null) {
                    throw new RuntimeException("Option \"{$option->getName()}\" requires a value");
                }

                if ($option->valueIsOptional() && $value == null) {
                    $value = $option->getDefaultValue();
                }

                $command->setOptionValue($option->getName(), $value);
            }
        }
    }

    /**
     * Gets whether or not there are too many argument values
     *
     * @param array $argumentValues The list of argument values
     * @param ICommand[] $commandArguments The list of command arguments
     * @return bool True if there are too many arguments, otherwise false
     */
    private function hasTooManyArguments(array $argumentValues, array $commandArguments) : bool
    {
        if (count($argumentValues) > count($commandArguments)) {
            // Only when the last argument is an array do we allow more request arguments than command arguments
            if (count($commandArguments) == 0 || !end($commandArguments)->isArray()) {
                return true;
            }
        }

        return false;
    }
}