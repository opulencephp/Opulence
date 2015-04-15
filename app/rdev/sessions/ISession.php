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
     * Flushes all the session variables
     */
    public function flush();

    /**
     * Gets the value of a variable
     *
     * @param string $name The name of the variable to get
     * @return mixed|null The value of the variable if it exists, otherwise null
     */
    public function get($name);

    /**
     * Gets the session Id
     *
     * @return int|string The session Id
     */
    public function getId();

    /**
     * Gets whether or not the session has started
     *
     * @return bool True if the session has started, otherwise false
     */
    public function hasStarted();

    /**
     * Regenerates the Id
     */
    public function regenerateId();

    /**
     * Sets the value of a variable
     *
     * @param string $name The name of the variable to set
     * @param mixed $value The value of the variable
     */
    public function set($name, $value);

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