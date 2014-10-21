<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for config classes to implement
 */
namespace RDev\Models\Configs;

interface IConfig extends \ArrayAccess, \IteratorAggregate, \Serializable, \Countable
{
    /**
     * Sets the config array
     *
     * @param array $configArray The config array to use
     * @throws \RuntimeException Thrown if the config is invalid
     */
    public function exchangeArray($configArray);

    /**
     * Converts the config settings to a keyed array
     *
     * @return array The config settings as a keyed array
     */
    public function getArrayCopy();
} 