<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Environments;

/**
 * Defines an environment
 */
class Environment
{
    /** The production environment */
    const PRODUCTION = 'production';
    /** The staging environment */
    const STAGING = 'staging';
    /** The testing environment */
    const TESTING = 'testing';
    /** The development environment */
    const DEVELOPMENT = 'development';

    /**
     * Gets the value of an environment variable
     *
     * @param string $name The name of the environment variable to get
     * @param mixed $default The default value if none existed
     * @return string|null The value of the environment value if one was set, otherwise null
     */
    public static function getVar(string $name, $default = null)
    {
        if (array_key_exists($name, $_ENV)) {
            return $_ENV[$name];
        } elseif (array_key_exists($name, $_SERVER)) {
            return $_SERVER[$name];
        } else {
            $value = getenv($name);

            if ($value === false) {
                return $default;
            }

            return $value;
        }
    }

    /**
     * Gets whether or not the application is running in a console
     *
     * @return bool True if the application is running in a console, otherwise false
     */
    public static function isRunningInConsole() : bool
    {
        return php_sapi_name() === 'cli';
    }

    /**
     * Sets an environment variable, but does not overwrite existing variables
     *
     * @param string $name The name of the environment variable to set
     * @param mixed $value The value
     */
    public static function setVar(string $name, $value)
    {
        // Do not overwrite the variable if it already exists
        if (self::getVar($name) === null) {
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}
