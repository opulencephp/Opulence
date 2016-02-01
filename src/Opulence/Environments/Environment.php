<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Environments;

/**
 * Defines an environment
 */
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
    public function __construct(string $name)
    {
        $this->setName($name);
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Gets the value of an environment variable
     *
     * @param string $name The name of the environment variable to get
     * @return string|null The value of the environment value if one was set, otherwise null
     */
    public function getVar(string $name)
    {
        if (array_key_exists($name, $_ENV)) {
            return $_ENV[$name];
        } elseif (array_key_exists($name, $_SERVER)) {
            return $_SERVER[$name];
        } else {
            $value = getenv($name);

            if ($value === false) {
                return null;
            }

            return $value;
        }
    }

    /**
     * Gets whether or not the application is running in a console
     *
     * @return bool True if the application is running in a console, otherwise false
     */
    public function isRunningInConsole() : bool
    {
        return php_sapi_name() == "cli";
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Sets an environment variable
     *
     * @param string $name The name of the environment variable to set
     * @param mixed $value The value
     */
    public function setVar(string $name, $value)
    {
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}