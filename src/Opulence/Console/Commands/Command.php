<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Console\Commands;

use InvalidArgumentException;
use Opulence\Console\Requests\Argument;
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Responses\IResponse;
use RuntimeException;

/**
 * Defines a basic command
 */
abstract class Command implements ICommand
{
    /** @var string The name of the command */
    protected $name = '';
    /** @var string A brief description of the command */
    protected $description = '';
    /** @var Argument[] The list of arguments */
    protected $arguments = [];
    /** @var Option[] The list of options */
    protected $options = [];
    /** @var array The mapping of argument names to values */
    protected $argumentValues = [];
    /** @var array The mapping of option names to values */
    protected $optionValues = [];
    /** @var string The help text to be displayed in the help command */
    protected $helpText = '';
    /** @var CommandCollection The list of registered commands */
    protected $commandCollection = null;
    /** @var bool Whether or not the base class' constructor was called */
    private $constructorCalled = false;

    /**
     * To ensure that the command is properly instantiated, be sure to
     * always call parent::__construct() in child command classes
     *
     * @throws InvalidArgumentException Thrown if the name is not set
     */
    public function __construct()
    {
        $this->constructorCalled = true;

        // Define the command
        $this->define();

        if (empty($this->name)) {
            throw new InvalidArgumentException('Command name cannot be empty');
        }

        // This adds a help option to all commands
        $this->addOption(new Option(
            'help',
            'h',
            OptionTypes::NO_VALUE,
            'Displays info about the command'
        ));
    }

    /**
     * @inheritdoc
     */
    public function addArgument(Argument $argument) : ICommand
    {
        $this->arguments[$argument->getName()] = $argument;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addOption(Option $option) : ICommand
    {
        $this->options[$option->getName()] = $option;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function argumentValueIsSet(string $name) : bool
    {
        return isset($this->argumentValues[$name]);
    }

    /**
     * @inheritdoc
     */
    final public function execute(IResponse $response)
    {
        if (!$this->constructorCalled) {
            throw new RuntimeException('Command class "' . static::class . '" does not call parent::__construct()');
        }

        return $this->doExecute($response);
    }

    /**
     * @inheritdoc
     */
    public function getArgument(string $name) : Argument
    {
        if (!isset($this->arguments[$name])) {
            throw new InvalidArgumentException("No argument with name \"$name\" exists");
        }

        return $this->arguments[$name];
    }

    /**
     * @inheritdoc
     */
    public function getArgumentValue(string $name)
    {
        if (!$this->argumentValueIsSet($name)) {
            throw new InvalidArgumentException("No argument with name \"$name\" exists");
        }

        return $this->argumentValues[$name];
    }

    /**
     * @inheritdoc
     */
    public function getArguments() : array
    {
        return array_values($this->arguments);
    }

    /**
     * @inheritdoc
     */
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * @inheritdoc
     */
    public function getHelpText() : string
    {
        return $this->helpText;
    }

    /**
     * @inheritdoc
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getOption(string $name) : Option
    {
        if (!isset($this->options[$name])) {
            throw new InvalidArgumentException("No option with name \"$name\" exists");
        }

        return $this->options[$name];
    }

    /**
     * @inheritdoc
     */
    public function getOptionValue(string $name)
    {
        if (!isset($this->options[$name])) {
            throw new InvalidArgumentException("No option with name \"$name\" exists");
        }

        if (!isset($this->optionValues[$name])) {
            return null;
        }

        return $this->optionValues[$name];
    }

    /**
     * @inheritdoc
     */
    public function getOptions() : array
    {
        return array_values($this->options);
    }

    /**
     * @inheritdoc
     */
    public function optionIsSet(string $name) : bool
    {
        // Don't use isset because the value very well might be null, in which case we'd still return true
        return array_key_exists($name, $this->optionValues);
    }

    /**
     * @inheritdoc
     */
    public function setArgumentValue(string $name, $value)
    {
        $this->argumentValues[$name] = $value;
    }

    /**
     * @inheritdoc
     */
    public function setCommandCollection(CommandCollection $commandCollection)
    {
        $this->commandCollection = $commandCollection;
    }

    /**
     * @inheritdoc
     */
    public function setOptionValue(string $name, $value)
    {
        $this->optionValues[$name] = $value;
    }

    /**
     * Sets the arguments and options for this command
     * Provides a convenient place to write down the definition for a command
     */
    abstract protected function define();

    /**
     * Actually executes the command
     *
     * @param IResponse $response The console response to write to
     * @return int|null Null or the status code of the command
     */
    abstract protected function doExecute(IResponse $response);

    /**
     * Sets the description of the command
     *
     * @param string $description The description to use
     * @return self For method chaining
     */
    protected function setDescription(string $description) : self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Sets the help text
     *
     * @param string $helpText The help text
     * @return self for method chaining
     */
    protected function setHelpText(string $helpText) : self
    {
        $this->helpText = $helpText;

        return $this;
    }

    /**
     * Sets the name of the command
     *
     * @param string $name The name to use
     * @return self For method chaining
     */
    protected function setName(string $name) : self
    {
        $this->name = $name;

        return $this;
    }
}
