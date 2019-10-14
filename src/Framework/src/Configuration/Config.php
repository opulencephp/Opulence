<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Configuration;

/**
 * Defines the config reader
 */
class Config
{
    /** @var array The list of settings by category */
    private static array $settings = [];

    /**
     * Gets a setting
     *
     * @param string $category The category of setting to get
     * @param string $setting The name of the setting to get
     * @param null $default The default value if one does not exist
     * @return mixed The value of the setting
     */
    public static function get(string $category, string $setting, $default = null)
    {
        if (!isset(self::$settings[$category][$setting])) {
            return $default;
        }

        return self::$settings[$category][$setting];
    }

    /**
     * Gets whether or not a setting has a value
     *
     * @param string $category The category whose setting we're checking
     * @param string $setting The setting to check for
     * @return bool True if the setting exists, otherwise false
     */
    public static function has(string $category, string $setting): bool
    {
        return isset(self::$settings[$category]) && isset(self::$settings[$category][$setting]);
    }

    /**
     * Sets a setting
     *
     * @param string $category The category whose setting we're changing
     * @param string $setting The name of the setting to set
     * @param mixed $value The value of the setting
     */
    public static function set(string $category, string $setting, $value): void
    {
        if (!isset(self::$settings[$category])) {
            self::$settings[$category] = [];
        }

        self::$settings[$category][$setting] = $value;
    }

    /**
     * Sets an entire category's settings (overwrites previous settings)
     *
     * @param string $category The category whose settings we're changing
     * @param array $settings The array of settings
     */
    public static function setCategory(string $category, array $settings): void
    {
        self::$settings[$category] = $settings;
    }
}
