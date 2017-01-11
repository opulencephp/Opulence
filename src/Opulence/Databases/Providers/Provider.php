<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Databases\Providers;

/**
 * Defines the base provider for RDBMS providers to extend
 */
class Provider
{
    /** @var string The format for date strings */
    protected $dateFormat = 'Y-m-d';
    /** @var string The format for time with time zone strings */
    protected $timeWithTimeZoneFormat = 'H:i:sO';
    /** @var string The format for time without time zone strings */
    protected $timeWithoutTimeZoneFormat = 'H:i:s';
    /** @var string The format for timestamps with timezones */
    protected $timestampWithTimeZoneFormat = 'Y-m-d H:i:sO';
    /** @var string The format for timestamps without timezones */
    protected $timestampWithoutTimeZoneFormat = 'Y-m-d H:i:s';

    /**
     * Converts an SQL boolean to a PHP boolean
     *
     * @param mixed $value The value to convert
     * @return bool|null The boolean value if the input was not null, otherwise null
     */
    public function convertFromSqlBoolean($value)
    {
        if ($value === null) {
            return null;
        }

        return (bool)$value;
    }

    /**
     * Converts a PHP boolean to an SQL boolean
     *
     * @param bool $value The boolean value to convert
     * @return int The boolean in an SQL boolean format
     */
    public function convertToSqlBoolean(bool $value)
    {
        if (is_bool($value)) {
            return (int)$value;
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getDateFormat() : string
    {
        return $this->dateFormat;
    }

    /**
     * @return string
     */
    public function getTimeWithTimeZoneFormat() : string
    {
        return $this->timeWithTimeZoneFormat;
    }

    /**
     * @return string
     */
    public function getTimeWithoutTimeZoneFormat() : string
    {
        return $this->timeWithoutTimeZoneFormat;
    }

    /**
     * @return string
     */
    public function getTimestampWithTimeZoneFormat() : string
    {
        return $this->timestampWithTimeZoneFormat;
    }

    /**
     * @return string
     */
    public function getTimestampWithoutTimeZoneFormat() : string
    {
        return $this->timestampWithoutTimeZoneFormat;
    }
}
