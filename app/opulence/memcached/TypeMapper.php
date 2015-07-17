<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a type mapper that can go back and forth between Memcached types and PHP types
 */
namespace Opulence\Memcached;
use DateTime;

class TypeMapper
{
    /**
     * Converts a Memcached boolean to a PHP boolean
     *
     * @param int $boolean The Memcached boolean to convert from
     * @return bool The PHP boolean
     */
    public function fromMemcachedBoolean($boolean)
    {
        return $boolean == 1;
    }

    /**
     * Converts a Memcached Unix timestamp to a PHP timestamp
     *
     * @param int $timestamp The Unix timestamp to convert from
     * @return DateTime The PHP timestamp
     */
    public function fromMemcachedTimestamp($timestamp)
    {
        return DateTime::createFromFormat("U", $timestamp);
    }

    /**
     * Converts a PHP boolean to a Memcached boolean
     *
     * @param bool $boolean The PHP boolean to convert
     * @return int The Memcached boolean
     */
    public function toMemcachedBoolean($boolean)
    {
        if($boolean)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    /**
     * Converts a PHP timestamp to a Memcached Unix timestamp
     *
     * @param DateTime $timestamp The PHP timestamp to convert
     * @return int The Unix timestamp
     */
    public function toMemcachedTimestamp(DateTime $timestamp)
    {
        return $timestamp->getTimestamp();
    }
} 