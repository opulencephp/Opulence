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
     * Instantiates a config from a keyed array
     *
     * @param array $configArray The keyed config array to instantiate the config from
     * @return IConfig The config object
     */
    public static function fromArray(array $configArray);

    /**
     * Converts the config settings to a keyed array
     *
     * @return array The config settings as a keyed array
     */
    public function toArray();
} 