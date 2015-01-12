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
    /** @var string The help text to be displayed in the help command */
    protected $helpText = "";

    /**
     * To ensure that the command is properly instantiated, be sure to
     * always call parent::__construct() in child command classes
     *
     * @throws \InvalidArgumentException Thrown if the name is not set
     */
    public function __construct()
    {
        // Define the command
        $this->define();

        if(empty($this->name))
        {
            throw new \InvalidArgumentException("Command name cannot be empty");
        }

        // This adds a help option to all commands
        $this->addOption(new Requests\Option(
            "help",
            "h",
            Requests\OptionTypes::NO_VALUE,
            "Displays info about the command"
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function addArgument(Requests\Argument $argument)
    {
        $this->arguments[$argument->getName()] = $argument;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addOption(Requests\Option $option)
    {
        $this->options[$option->getName()] = $option;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function argumentHasValue($name)
    {
        return isset($this->argumentValues[$name]);
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
        if(!$this->argumentHasValue($name))
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
    public function getHelpText()
    {
        return $this->helpText;
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
        if(!isset($this->options[$name]))
        {
            throw new \InvalidArgumentException("No option with name \"$name\" exists");
        }

        if(!isset($this->optionValues[$name]))
        {
            return null;
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
    public function optionIsSet($name)
    {
        // Don't use isset because the value very well might be null, in which case we'd still return true
        return array_key_exists($name, $this->optionValues);
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
    public function setHelpText($helpText)
    {
        $this->helpText = $helpText;
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
    abstract protected function define();

    /**
     * Sets the description of the command
     *
     * @param string $description The description to use
     * @return Command For method chaining
     */
    protected function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Sets the name of the command
     *
     * @param string $name The name to use
     * @return Command For method chaining
     */
    protected function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}