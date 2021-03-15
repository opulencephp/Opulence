<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Console\Commands;

use InvalidArgumentException;
use Opulence\Console\Requests\Argument;
use Opulence\Console\Requests\Option;
use Opulence\Console\Responses\IResponse;
use RuntimeException;

/**
 * Defines the interface for console commands to implement
 */
interface ICommand
{
    /**
     * Adds an argument to the command
     *
     * @param Argument $argument The argument to add
     * @return self For method chaining
     */
    public function addArgument(Argument $argument) : self;

    /**
     * Adds an option to the command
     *
     * @param Option $option The option to add
     * @return self For method chaining
     */
    public function addOption(Option $option) : self;

    /**
     * Gets whether or not an argument has a value
     *
     * @param string $name The name of the argument to check
     * @return bool True if the input argument has a value, otherwise false
     */
    public function argumentValueIsSet(string $name) : bool;

    /**
     * Executes the command
     *
     * @param IResponse $response The console response to write to
     * @return int|null Null or the status code of the command
     * @throws RuntimeException Thrown if the command was not setup correctly or could not be executed
     */
    public function execute(IResponse $response);

    /**
     * Gets the argument with the input name
     *
     * @param string $name The name to look for
     * @return Argument The argument with the input name
     * @throws InvalidArgumentException Thrown if no argument exists with that name
     */
    public function getArgument(string $name) : Argument;

    /**
     * Gets the value of an argument
     *
     * @param string $name The name of the argument to get
     * @return mixed The value of the argument
     * @throws InvalidArgumentException Thrown if there is no argument with the input name
     */
    public function getArgumentValue(string $name);

    /**
     * Gets the list of arguments this command accepts
     *
     * @return Argument[] The list of arguments
     */
    public function getArguments() : array;

    /**
     * Gets the description of the command
     *
     * @return string The description
     */
    public function getDescription() : string;

    /**
     * Gets the help text
     *
     * @return string The help text
     */
    public function getHelpText() : string;

    /**
     * Gets the name of the command
     *
     * @return string The name
     */
    public function getName() : string;

    /**
     * Gets the option with the input name
     *
     * @param string $name The name to look for
     * @return Option The option with the input name
     * @throws InvalidArgumentException Thrown if no option exists with that name
     */
    public function getOption(string $name) : Option;

    /**
     * Gets the value of an option
     *
     * @param string $name The name of the option to get
     * @return mixed The value of the option
     * @throws InvalidArgumentException Thrown if there is no option with the input name
     */
    public function getOptionValue(string $name);

    /**
     * Gets the list of options this command accepts
     *
     * @return Option[] The list of options
     */
    public function getOptions() : array;

    /**
     * Gets whether or not the command has a certain option set
     * Note that this does not necessarily mean it has a value - just that it was set
     *
     * @param string $name The name of the option to check
     * @return bool True if the option is set, otherwise false
     */
    public function optionIsSet(string $name) : bool;

    /**
     * Sets the value of an argument
     *
     * @param string $name The name of the argument to set
     * @param mixed $value The value to set
     */
    public function setArgumentValue(string $name, $value);

    /**
     * Sets the list of registered commands
     *
     * @param CommandCollection $commandCollection The list of registered commands
     */
    public function setCommandCollection(CommandCollection $commandCollection);

    /**
     * Sets the value of an option
     *
     * @param string $name The name of the option to set
     * @param mixed $value The value to set
     */
    public function setOptionValue(string $name, $value);
}
