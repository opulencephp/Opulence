<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for config classes to implement
 */
namespace RDev\Models\Configs;

interface IConfig
{
    /**
     * Sets the config array
     *
     * @param array $configArray The config array to use
     */
    public function fromArray(array $configArray);

    /**
     * Gets whether or not this config is valid
     * This is a good place for implementing classes to implement some sort of validation on the existence of certain
     * keys and values
     *
     * @return bool True if the config is valid, otherwise false
     */
    public function isValid();

    /**
     * Converts the config settings to a keyed array
     *
     * @return array The config settings as a keyed array
     */
    public function toArray();
} 