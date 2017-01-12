<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Memcached\Types;

use DateTime;
use DateTimeInterface;
use DateTimeZone;

/**
 * Defines a type mapper that can go back and forth between Memcached types and PHP types
 */
class TypeMapper
{
    /**
     * Converts a Memcached boolean to a PHP boolean
     *
     * @param int $boolean The Memcached boolean to convert from
     * @return bool The PHP boolean
     */
    public function fromMemcachedBoolean($boolean) : bool
    {
        return $boolean == 1;
    }

    /**
     * Converts a Memcached Unix timestamp to a PHP timestamp
     *
     * @param int $timestamp The Unix timestamp to convert from
     * @return DateTime The PHP timestamp
     */
    public function fromMemcachedTimestamp($timestamp) : DateTime
    {
        $date = DateTime::createFromFormat('U', $timestamp);
        $date->setTimezone(new DateTimeZone(date_default_timezone_get()));

        return $date;
    }

    /**
     * Converts a PHP boolean to a Memcached boolean
     *
     * @param bool $boolean The PHP boolean to convert
     * @return int The Memcached boolean
     */
    public function toMemcachedBoolean(bool $boolean) : int
    {
        if ($boolean) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Converts a PHP timestamp to a Memcached Unix timestamp
     *
     * @param DateTimeInterface $timestamp The PHP timestamp to convert
     * @return int The Unix timestamp
     */
    public function toMemcachedTimestamp(DateTimeInterface $timestamp) : int
    {
        return $timestamp->getTimestamp();
    }
}
