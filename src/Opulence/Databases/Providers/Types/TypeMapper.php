<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Databases\Providers\Types;

use DateTime;
use Exception;
use InvalidArgumentException;
use Opulence\Databases\Providers\Provider;
use RuntimeException;

/**
 * Defines a type mapper that can go back and forth between provider types and PHP types
 */
class TypeMapper
{
    /** @var Provider The default provider to use in the case that one isn't specified in a method call */
    protected $provider = null;

    /**
     * @param Provider $provider The default provider to use in the case that one isn't specified in a method call
     */
    public function __construct(Provider $provider = null)
    {
        if ($provider !== null) {
            $this->provider = $provider;
        }
    }

    /**
     * Converts an SQL boolean to a PHP boolean
     *
     * @param mixed $sqlBoolean The boolean to convert
     * @param Provider $provider The provider to convert from
     * @return bool|null The PHP boolean if it was a boolean value, otherwise null
     */
    public function fromSqlBoolean($sqlBoolean, Provider $provider = null)
    {
        $this->setParameterProvider($provider);

        return $provider->convertFromSqlBoolean($sqlBoolean);
    }

    /**
     * Converts an SQL date to a PHP date time
     *
     * @param string $sqlDate The date to convert
     * @param Provider $provider The provider to convert from
     * @return DateTime|null The PHP date
     * @throws InvalidArgumentException Thrown if the input date couldn't be cast to a PHP date
     */
    public function fromSqlDate($sqlDate, Provider $provider = null)
    {
        if ($sqlDate === null) {
            return null;
        }

        $this->setParameterProvider($provider);
        // The "!" zeroes out the hours, minutes, and seconds
        $phpDate = DateTime::createFromFormat("!" . $provider->getDateFormat(), $sqlDate);

        if ($phpDate === false) {
            $phpDate = $this->parseUnknownDateTimeFormat($sqlDate);
        }

        return $phpDate;
    }

    /**
     * Converts an SQL JSON string to a PHP array
     *
     * @param string $json The JSON string to convert
     * @param Provider $provider The provider to convert from
     * @return array The PHP array
     */
    public function fromSqlJson($json, Provider $provider = null)
    {
        if ($json === null) {
            return [];
        }

        return json_decode($json, true);
    }

    /**
     * Converts an SQL time with time zone to a PHP date time
     *
     * @param string $sqlTime The time to convert
     * @param Provider $provider The provider to convert from
     * @return DateTime|null The PHP time
     * @throws InvalidArgumentException Thrown if the input time couldn't be cast to a PHP time
     */
    public function fromSqlTimeWithTimeZone($sqlTime, Provider $provider = null)
    {
        if ($sqlTime === null) {
            return null;
        }

        $this->setParameterProvider($provider);
        $phpTime = DateTime::createFromFormat($provider->getTimeWithTimeZoneFormat(), $sqlTime);

        if ($phpTime === false) {
            $phpTime = $this->parseUnknownDateTimeFormat($sqlTime);
        }

        return $phpTime;
    }

    /**
     * Converts an SQL time without time zone to a PHP date time
     *
     * @param string $sqlTime The time to convert
     * @param Provider $provider The provider to convert from
     * @return DateTime|null The PHP time
     * @throws InvalidArgumentException Thrown if the input time couldn't be cast to a PHP time
     */
    public function fromSqlTimeWithoutTimeZone($sqlTime, Provider $provider = null)
    {
        if ($sqlTime === null) {
            return null;
        }

        $this->setParameterProvider($provider);
        $phpTime = DateTime::createFromFormat($provider->getTimeWithoutTimeZoneFormat(), $sqlTime);

        if ($phpTime === false) {
            $phpTime = $this->parseUnknownDateTimeFormat($sqlTime);
        }

        return $phpTime;
    }

    /**
     * Converts an SQL timestamp with time zone to a PHP date time
     *
     * @param string $sqlTimestamp The timestamp with time zone to convert
     * @param Provider $provider The provider to convert from
     * @return DateTime|null The PHP date time
     * @throws InvalidArgumentException Thrown if the input timestamp couldn't be cast to a PHP timestamp
     */
    public function fromSqlTimestampWithTimeZone($sqlTimestamp, Provider $provider = null)
    {
        if ($sqlTimestamp === null) {
            return null;
        }

        $this->setParameterProvider($provider);

        $phpTimestamp = DateTime::createFromFormat($provider->getTimestampWithTimeZoneFormat(), $sqlTimestamp);

        if ($phpTimestamp === false) {
            $phpTimestamp = $this->parseUnknownDateTimeFormat($sqlTimestamp);
        }

        return $phpTimestamp;
    }

    /**
     * Converts an SQL timestamp without time zone to a PHP date time
     *
     * @param string $sqlTimestamp The timestamp without time zone to convert
     * @param Provider $provider The provider to convert from
     * @return DateTime|null The PHP date time
     * @throws InvalidArgumentException Thrown if the input timestamp couldn't be cast to a PHP timestamp
     */
    public function fromSqlTimestampWithoutTimeZone($sqlTimestamp, Provider $provider = null)
    {
        if ($sqlTimestamp === null) {
            return null;
        }

        $this->setParameterProvider($provider);
        $phpTimestamp = DateTime::createFromFormat($provider->getTimestampWithoutTimeZoneFormat(), $sqlTimestamp);

        if ($phpTimestamp === false) {
            $phpTimestamp = $this->parseUnknownDateTimeFormat($sqlTimestamp);
        }

        return $phpTimestamp;
    }

    /**
     * Converts a PHP boolean to an SQL boolean
     *
     * @param bool $boolean The boolean to convert
     * @param Provider $provider The provider to convert to
     * @return mixed The SQL boolean suitable for database storage
     */
    public function toSqlBoolean($boolean, Provider $provider = null)
    {
        $this->setParameterProvider($provider);

        return $provider->convertToSqlBoolean($boolean);
    }

    /**
     * Converts a PHP date time to an SQL date
     *
     * @param DateTime $date The date time to convert
     * @param Provider $provider The provider to convert to
     * @return string The SQL date suitable for database storage
     */
    public function toSqlDate(DateTime $date, Provider $provider = null)
    {
        $this->setParameterProvider($provider);

        return $date->format($provider->getDateFormat());
    }

    /**
     * Converts a PHP array to SQL JSON
     *
     * @param array $json The array to convert
     * @param Provider $provider The provider to convert to
     * @return string The SQL JSON string suitable for database storage
     */
    public function toSqlJson(array $json, Provider $provider = null)
    {
        return json_encode($json);
    }

    /**
     * Converts a PHP date time with time zone to an SQL time
     *
     * @param DateTime $time The date time to convert
     * @param Provider $provider The provider to convert to
     * @return string The SQL time suitable for database storage
     */
    public function toSqlTimeWithTimeZone(DateTime $time, Provider $provider = null)
    {
        $this->setParameterProvider($provider);

        return $time->format($provider->getTimeWithTimeZoneFormat());
    }

    /**
     * Converts a PHP date time without time zone to an SQL time
     *
     * @param DateTime $time The date time to convert
     * @param Provider $provider The provider to convert to
     * @return string The SQL time suitable for database storage
     */
    public function toSqlTimeWithoutTimeZone(DateTime $time, Provider $provider = null)
    {
        $this->setParameterProvider($provider);

        return $time->format($provider->getTimeWithoutTimeZoneFormat());
    }

    /**
     * Converts a PHP date time to an SQL timestamp with time zone
     *
     * @param DateTime $timestamp The date time to convert
     * @param Provider $provider The provider to convert to
     * @return string The SQL timestamp with time zone suitable for database storage
     */
    public function toSqlTimestampWithTimeZone(DateTime $timestamp, Provider $provider = null)
    {
        $this->setParameterProvider($provider);

        return $timestamp->format($provider->getTimestampWithTimeZoneFormat());
    }

    /**
     * Converts a PHP date time to an SQL timestamp without time zone
     *
     * @param DateTime $timestamp The date time to convert
     * @param Provider $provider The provider to convert to
     * @return string The SQL timestamp without time zone suitable for database storage
     */
    public function toSqlTimestampWithoutTimeZone(DateTime $timestamp, Provider $provider = null)
    {
        $this->setParameterProvider($provider);

        return $timestamp->format($provider->getTimestampWithoutTimeZoneFormat());
    }

    /**
     * Attempts to parse an unknown date/time format
     *
     * @param string $sqlDateTime The date/time to parse
     * @return DateTime The PHP date time
     * @throws InvalidArgumentException Thrown if the input time could not be parsed
     */
    protected function parseUnknownDateTimeFormat($sqlDateTime)
    {
        try {
            return new DateTime($sqlDateTime);
        } catch (Exception $ex) {
            throw new InvalidArgumentException("Unable to cast timestamp: " . $ex->getMessage());
        }
    }

    /**
     * Checks to see that at least the object's provider is set or the input provider is set
     * If the input provider is not set, then it is set by reference to the object's provider
     *
     * @param Provider $provider The provider to set
     * @throws RuntimeException Thrown if neither the input provider nor the object provider are specified
     */
    protected function setParameterProvider(Provider &$provider = null)
    {
        if ($provider === null) {
            if ($this->provider === null) {
                throw new RuntimeException("No provider specified");
            }

            $provider = $this->provider;
        }
    }
} 