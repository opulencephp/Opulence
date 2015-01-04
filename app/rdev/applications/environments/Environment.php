<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines an application environment
 */
namespace RDev\Applications\Environments;

class Environment
{
    /** The production environment */
    const PRODUCTION = "production";
    /** The staging environment */
    const STAGING = "staging";
    /** The testing environment */
    const TESTING = "testing";
    /** The development environment */
    const DEVELOPMENT = "development";

    /** @var string The name of the environment */
    private $name = "";

    /**
     * @param string $name The name of the environment
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the value of an environment variable
     *
     * @param string $name The name of the environment variable to get
     * @return string|null The value of the environment value if one was set, otherwise null
     */
    public function getVariable($name)
    {
        $value = getenv($name);

        if($value === false)
        {
            return null;
        }

        return $value;
    }

    /**
     * Gets whether or not the application is running in a console
     *
     * @return bool true if the application is running in a console, otherwise false
     */
    public function isRunningInConsole()
    {
        return php_sapi_name() == "cli";
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Sets an environment variable
     *
     * @param string $name The name of the environment variable to set
     * @param mixed $value The value
     */
    public function setVariable($name, $value)
    {
        putenv($name . "=" . $value);
    }
}