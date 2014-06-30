<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a type mapper that can go back and forth between Memcached types and PHP types
 */
namespace RDev\Models\Databases\NoSQL\Memcached;

class TypeMapper
{
    /**
     * Converts a Memcached Unix timestamp to a PHP timestamp
     *
     * @param int $timestamp The Unix timestamp to convert from
     * @return \DateTime The PHP timestamp
     */
    public function fromMemcachedTimestamp($timestamp)
    {
        return \DateTime::createFromFormat("U", $timestamp, new \DateTimeZone("UTC"));
    }

    /**
     * Converts a PHP timestamp to a Memcached Unix timestamp
     *
     * @param \DateTime $timestamp The PHP timestamp to convert
     * @return int The Unix timestamp
     */
    public function toMemcachedTimestamp(\DateTime $timestamp)
    {
        return $timestamp->getTimestamp();
    }
} 