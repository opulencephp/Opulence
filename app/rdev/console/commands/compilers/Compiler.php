<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a command compiler
 */
namespace RDev\Console\Commands\Compilers;
use RDev\Console\Commands;
use RDev\Console\Requests;

class Compiler implements ICompiler
{
    /**
     * {@inheritdoc}
     */
    public function compile(Commands\ICommand $command, Requests\IRequest $request)
    {
        $this->compileArguments($command, $request);
        $this->compileOptions($command, $request);

        return $command;
    }

    /**
     * Compiles arguments in a command
     *
     * @param Commands\ICommand $command The command to compile
     * @param Requests\IRequest $request The user request
     */
    protected function compileArguments(Commands\ICommand &$command, Requests\IRequest $request)
    {
        $argumentValues = $request->getArgumentValues();
        $hasSetArrayArgument = false;

        foreach($command->getArguments() as $argument)
        {
            if(count($argumentValues) == 0)
            {
                if(!$argument->isOptional())
                {
                    throw new \RuntimeException("Argument \"{$argument->getName()}\" does not have default value");
                }

                $command->setArgumentValue($argument->getName(), $argument->getDefaultValue());
            }
            else
            {
                if($hasSetArrayArgument)
                {
                    throw new \RuntimeException("Array argument must appear at end of list of arguments");
                }

                if($argument->isArray())
                {
                    // Add the rest of the values in the request to this argument
                    $restOfArgumentValues = [];

                    while(count($argumentValues) > 0)
                    {
                        $restOfArgumentValues[] = array_shift($argumentValues);
                    }

                    $command->setArgumentValue($argument->getName(), $restOfArgumentValues);
                    $hasSetArrayArgument = true;
                }
                else
                {
                    $command->setArgumentValue($argument->getName(), array_shift($argumentValues));
                }
            }
        }
    }

    /**
     * Compiles options in a command
     *
     * @param Commands\ICommand $command The command to compile
     * @param Requests\IRequest $request The user request
     */
    protected function compileOptions(Commands\ICommand &$command, Requests\IRequest $request)
    {
        foreach($command->getOptions() as $option)
        {
            $shortNameIsSet = $request->optionIsSet($option->getShortName());
            $longNameIsSet = $request->optionIsSet($option->getName());

            // All options are optional (duh)
            if($shortNameIsSet || $longNameIsSet)
            {
                if($longNameIsSet)
                {
                    $value = $request->getOptionValue($option->getName());
                }
                else
                {
                    $value = $request->getOptionValue($option->getShortName());
                }

                if(!$option->valueIsPermitted() && $value !== null)
                {
                    throw new \RuntimeException("Option \"{$option->getName()}\" does not permit a value");
                }

                if($option->valueIsRequired() && $value === null)
                {
                    throw new \RuntimeException("Option \"{$option->getName()}\" requires a value");
                }

                if($option->valueIsOptional() && $value == null)
                {
                    $value = $option->getDefaultValue();
                }

                $command->setOptionValue($option->getName(), $value);
            }
        }
    }
}