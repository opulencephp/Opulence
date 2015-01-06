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
        $argumentValues = $request->getArgumentValues();

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
                $command->setArgumentValue($argument->getName(), array_shift($argumentValues));
            }
        }

        foreach($request->getOptions() as $name => $value)
        {
            $command->setOptionValue($name, $value);
        }

        return $command;
    }
}