<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a type mapper that can go back and forth between Redis types and PHP types
 */
namespace Opulence\Redis;
use DateTime;

class TypeMapper
{
    /**
     * Converts a Redis boolean to a PHP boolean
     *
     * @param int $boolean The Redis boolean to convert from
     * @return bool The PHP boolean
     */
    public function fromRedisBoolean($boolean)
    {
        return $boolean == 1;
    }

    /**
     * Converts a Redis Unix timestamp to a PHP timestamp
     *
     * @param int $timestamp The Unix timestamp to convert from
     * @return DateTime The PHP timestamp
     */
    public function fromRedisTimestamp($timestamp)
    {
        return DateTime::createFromFormat("U", $timestamp);
    }

    /**
     * Converts a PHP boolean to a Redis boolean
     *
     * @param bool $boolean The PHP boolean to convert
     * @return int The Redis boolean
     */
    public function toRedisBoolean($boolean)
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
     * Converts a PHP timestamp to a Redis Unix timestamp
     *
     * @param DateTime $timestamp The PHP timestamp to convert
     * @return int The Unix timestamp
     */
    public function toRedisTimestamp(DateTime $timestamp)
    {
        return $timestamp->getTimestamp();
    }
} 