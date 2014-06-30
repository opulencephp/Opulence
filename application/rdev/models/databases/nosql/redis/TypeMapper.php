<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a type mapper that can go back and forth between Redis types and PHP types
 */
namespace RDev\Models\Databases\NoSQL\Redis;

class TypeMapper
{
    /**
     * Converts a Redis Unix timestamp to a PHP timestamp
     *
     * @param int $timestamp The Unix timestamp to convert from
     * @return \DateTime The PHP timestamp
     */
    public function fromRedisTimestamp($timestamp)
    {
        return \DateTime::createFromFormat("U", $timestamp, new \DateTimeZone("UTC"));
    }

    /**
     * Converts a PHP timestamp to a Redis Unix timestamp
     *
     * @param \DateTime $timestamp The PHP timestamp to convert
     * @return int The Unix timestamp
     */
    public function toRedisTimestamp(\DateTime $timestamp)
    {
        return $timestamp->getTimestamp();
    }
} 