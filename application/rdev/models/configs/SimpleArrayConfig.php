<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a simple config that uses an array as its basis
 */
namespace RDev\Models\Configs;

class SimpleArrayConfig implements IConfig
{
    /** @var array The config as an array */
    protected $configArray = [];

    /**
     * @param array $configArray The config array to initialize from
     */
    public function __construct(array $configArray)
    {
        $this->configArray = $configArray;
    }

    /**
     * Instantiates a config from a keyed array
     *
     * @param array $configArray The keyed config array to instantiate the config from
     * @return IConfig The config object
     */
    public static function fromArray(array $configArray)
    {
        return new static($configArray);
    }

    /**
     * Converts the config settings to a keyed array
     *
     * @return array The config settings as a keyed array
     */
    public function toArray()
    {
        return $this->configArray;
    }
} 