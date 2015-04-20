<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for sessions to implement
 */
namespace RDev\Sessions;
use ArrayAccess;

interface ISession extends ArrayAccess
{
    /**
     * Marks newly flashed data as old, and old data is deleted
     */
    public function ageFlashData();

    /**
     * Deletes a variable
     *
     * @param string $key The name of the variable to delete
     */
    public function delete($key);

    /**
     * Flashes data for exactly one request
     *
     * @param string $key The name of the variable to set
     * @param mixed $value The value of the variable
     */
    public function flash($key, $value);

    /**
     * Flushes all the session variables
     */
    public function flush();

    /**
     * Gets the value of a variable
     *
     * @param string $key The name of the variable to get
     * @param mixed|null $defaultValue The default value to use if the variable does not exist
     * @return mixed|null The value of the variable if it exists, otherwise null
     */
    public function get($key, $defaultValue = null);

    /**
     * Gets the mapping of all session variable names to their values
     *
     * @return array The list of all session variables
     */
    public function getAll();

    /**
     * Gets the session Id
     *
     * @return int|string The session Id
     */
    public function getId();

    /**
     * Gets whether or not a session variable is set
     *
     * @param string $key The name of the variable to search for
     * @return bool True if the session has a variable, otherwise false
     */
    public function has($key);

    /**
     * Gets whether or not the session has started
     *
     * @return bool True if the session has started, otherwise false
     */
    public function hasStarted();

    /**
     * Reflashes all of the flash data
     */
    public function reflash();

    /**
     * Regenerates the Id
     */
    public function regenerateId();

    /**
     * Sets the value of a variable
     *
     * @param string $key The name of the variable to set
     * @param mixed $value The value of the variable
     */
    public function set($key, $value);

    /**
     * Sets the session Id
     *
     * @param int|string $id The session Id
     */
    public function setId($id);

    /**
     * Starts the session
     *
     * @param array $variables The list of variables in this session
     * @return bool True if the session started successfully
     */
    public function start(array $variables = []);
} 