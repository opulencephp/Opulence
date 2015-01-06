<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a basic command
 */
namespace RDev\Console\Commands;
use RDev\Console\Requests;
use RDev\Console\Responses;

abstract class Command implements ICommand
{
    /** @var string The name of the command */
    protected $name = "";
    /** @var string A brief description of the command */
    protected $description = "";
    /** @var Requests\Argument[] The list of arguments */
    protected $arguments = [];
    /** @var Requests\Option[] The list of options */
    protected $options = [];
    /** @var array The mapping of argument names to values */
    protected $argumentValues = [];
    /** @var array The mapping of option names to values */
    protected $optionValues = [];

    /**
     * @param string $name The name of the command
     * @param string $description A brief description of the command
     */
    public function __construct($name, $description)
    {
        $this->name = $name;
        $this->description = $description;
        // Setup the command
        $this->setArgumentsAndOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function addArgument(Requests\Argument $argument)
    {
        $this->arguments[$argument->getName()] = $argument;
    }

    /**
     * {@inheritdoc}
     */
    public function addOption(Requests\Option $option)
    {
        $this->options[$option->getName()] = $option;
    }

    /**
     * {@inheritdoc}
     */
    public function getArgument($name)
    {
        if(!isset($this->arguments[$name]))
        {
            throw new \InvalidArgumentException("No argument with name \"$name\" exists");
        }

        return $this->arguments[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getArgumentValue($name)
    {
        if(!isset($this->argumentValues[$name]))
        {
            throw new \InvalidArgumentException("No argument with name \"$name\" exists");
        }

        return $this->argumentValues[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments()
    {
        return array_values($this->arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($name)
    {
        if(!isset($this->options[$name]))
        {
            throw new \InvalidArgumentException("No option with name \"$name\" exists");
        }

        return $this->options[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionValue($name)
    {
        if(!isset($this->optionValues[$name]))
        {
            throw new \InvalidArgumentException("No option with name \"$name\" exists");
        }

        return $this->optionValues[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return array_values($this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function setArgumentValue($name, $value)
    {
        $this->argumentValues[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptionValue($name, $value)
    {
        $this->optionValues[$name] = $value;
    }

    /**
     * Sets the arguments and options for this command
     * Provides a convenient place to write down the definition for a command
     */
    abstract protected function setArgumentsAndOptions();
}