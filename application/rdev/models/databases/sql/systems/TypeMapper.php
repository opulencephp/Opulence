<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a type mapper that can go back and forth between RDBMS types and PHP types
 */
namespace RDev\Models\Databases\SQL\Systems;

class TypeMapper
{
    /**
     * Converts an SQL date to a PHP date time
     *
     * @param System $system The system to convert from
     * @param string $sqlDate The date to convert
     * @return \DateTime|null The PHP date time
     */
    public function fromSQLDate(System $system, $sqlDate)
    {
        if($sqlDate === null)
        {
            return null;
        }

        return \DateTime::createFromFormat($system->getDateFormat(), $sqlDate, new \DateTimeZone("UTC"));
    }

    /**
     * Converts an SQL time to a PHP date time
     *
     * @param System $system The system to convert from
     * @param string $sqlTime The time to convert
     * @return \DateTime|null The PHP date time
     */
    public function fromSQLTime(System $system, $sqlTime)
    {
        if($sqlTime === null)
        {
            return null;
        }

        return \DateTime::createFromFormat($system->getTimeFormat(), $sqlTime, new \DateTimeZone("UTC"));
    }

    /**
     * Converts an SQL timestamp without time zone to a PHP date time
     *
     * @param System $system The system to convert from
     * @param string $sqlTimestamp The timestamp without time zone to convert
     * @return \DateTime|null The PHP date time
     */
    public function fromSQLTimestampWithOutTimeZone(System $system, $sqlTimestamp)
    {
        if($sqlTimestamp === null)
        {
            return null;
        }

        return \DateTime::createFromFormat($system->getTimestampWithoutTimeZoneFormat(),
            $sqlTimestamp, new \DateTimeZone("UTC"));
    }

    /**
     * Converts an SQL timestamp with time zone to a PHP date time
     *
     * @param System $system The system to convert from
     * @param string $sqlTimestamp The timestamp with time zone to convert
     * @return \DateTime|null The PHP date time
     */
    public function fromSQLTimestampWithTimeZone(System $system, $sqlTimestamp)
    {
        if($sqlTimestamp === null)
        {
            return null;
        }

        return \DateTime::createFromFormat($system->getTimestampWithTimeZoneFormat(),
            $sqlTimestamp, new \DateTimeZone("UTC"));
    }

    /**
     * Converts a PHP date time to an SQL date
     *
     * @param System $system The system to convert to
     * @param \DateTime $date The date time to convert
     * @return string The SQL date suitable for database storage
     */
    public function toSQLDate(System $system, \DateTime $date)
    {
        return $date->format($system->getDateFormat());
    }

    /**
     * Converts a PHP date time to an SQL time
     *
     * @param System $system The system to convert to
     * @param \DateTime $time The date time to convert
     * @return string The SQL time suitable for database storage
     */
    public function toSQLTime(System $system, \DateTime $time)
    {
        return $time->format($system->getTimeFormat());
    }

    /**
     * Converts a PHP date time to an SQL timestamp with time zone
     *
     * @param System $system The system to convert to
     * @param \DateTime $timestamp The date time to convert
     * @return string The SQL timestamp with time zone suitable for database storage
     */
    public function toSQLTimestampWithTimeZone(System $system, \DateTime $timestamp)
    {
        return $timestamp->format($system->getTimestampWithTimeZoneFormat());
    }

    /**
     * Converts a PHP date time to an SQL timestamp without time zone
     *
     * @param System $system The system to convert to
     * @param \DateTime $timestamp The date time to convert
     * @return string The SQL timestamp without time zone suitable for database storage
     */
    public function toSQLTimestampWithoutTimeZone(System $system, \DateTime $timestamp)
    {
        return $timestamp->format($system->getTimestampWithoutTimeZoneFormat());
    }
} 