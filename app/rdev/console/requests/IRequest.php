<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for console inputs to implement
 */
namespace RDev\Console\Requests;

interface IRequest
{
    /**
     * Gets the value of an argument
     *
     * @param string $name The name of the argument
     * @return mixed The value of the argument
     * @throws \InvalidArgumentException Thrown if the argument does not exist
     */
    public function getArgument($name);

    /**
     * Gets all the values of arguments
     *
     * @return array The mapping of argument names to their values
     */
    public function getArguments();

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
     * Gets whether or not the input contains an argument
     *
     * @param string $name The name of the argument
     * @return bool True if the input has the argument, otherwise false
     */
    public function hasArgument($name);

    /**
     * Gets whether or not the input contains an option
     *
     * @param string $name The name of the option
     * @return bool True if the input has the option, otherwise false
     */
    public function hasOption($name);

    /**
     * Sets the value of an argument
     *
     * @param string $name The name of the argument
     * @param mixed $value The value of the argument
     */
    public function setArgument($name, $value);

    /**
     * Sets the value of an option
     *
     * @param string $name The name of the option
     * @param mixed $value The value of the option
     */
    public function setOption($name, $value);
}