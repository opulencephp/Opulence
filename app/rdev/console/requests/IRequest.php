<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for console requests to implement
 */
namespace RDev\Console\Requests;

interface IRequest
{
    /**
     * Adds an argument value
     *
     * @param mixed $value The value of the argument
     */
    public function addArgumentValue($value);

    /**
     * Gets all the values of arguments
     *
     * @return array The list of argument values
     */
    public function getArgumentValues();

    /**
     * Gets the name of the command the request calls
     *
     * @return string The name of the command the request calls
     */
    public function getCommandName();

    /**
     * Gets the value of an option
     *
     * @param string $name The name of the option
     * @return mixed The value of the option
     * @throws \InvalidArgumentException Thrown if the option does not exist
     */
    public function getOption($name);

    /**
     * Gets all the values of options
     *
     * @return array The mapping of option names to their values
     */
    public function getOptions();

    /**
     * Gets whether or not the input contains an option
     *
     * @param string $name The name of the option
     * @return bool True if the input has the option, otherwise false
     */
    public function hasOption($name);

    /**
     * Sets the name of the command the request calls
     *
     * @param string $name The name of the command the request calls
     */
    public function setCommandName($name);

    /**
     * Sets the value of an option
     *
     * @param string $name The name of the option
     * @param mixed $value The value of the option
     */
    public function setOption($name, $value);
}