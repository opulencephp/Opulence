<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a config reader, which can convert and validate JSON config files
 */
namespace RDev\Models\Configs;

abstract class Reader
{
    /**
     * Reads a config from a file
     *
     * @param string $path The path to the file's location
     * @param string $configClassName The name of the class that implements IConfig to save the input to
     * @return IConfig The config object from the file
     * @throws \RuntimeException Thrown if there was a problem reading from the file
     * @throws \InvalidArgumentException Thrown if the config class name doesn't point to a class that implements IConfig
     */
    abstract public function readFromFile($path, $configClassName = "RDev\\Models\\Configs\\Config");

    /**
     * Creates a config from input
     *
     * @param mixed $input The input to read from
     *      For example, this could be a PHP array, JSON, and XML string, etc
     * @param string $configClassName The name of the class that implements IConfig to save the input to
     * @return IConfig The config object from the input
     * @throws \RuntimeException Thrown if there was a problem decoding the input
     * @throws \InvalidArgumentException Thrown if the config class name doesn't point to a class that implements IConfig
     */
    abstract public function readFromInput($input, $configClassName = "RDev\\Models\\Configs\\Config");

    /**
     * Creates a config of the input type from a config array
     *
     * @param array $configArray The config array to create the config from
     * @param string $configClassName The fully-qualified name of the class that implements IConfig to save the config to
     * @return IConfig The config from the input array
     */
    protected function createConfigFromArrayAndClassName(array $configArray, $configClassName)
    {
        $config = new $configClassName();

        if(!$config instanceof IConfig)
        {
            throw new \InvalidArgumentException("The class \"$configClassName\" doesn't implement IConfig");
        }

        /** @var IConfig $config */
        $config->fromArray($configArray);

        return $config;
    }

    /**
     * Validates whether or not a path to a config is valid
     *
     * @param string $path The path to the config to validate
     * @throws \InvalidArgumentException Thrown if the path is not a string
     * @throws \RuntimeException Thrown if the path does not exist or is not readable
     */
    protected function validatePath($path)
    {
        if(!is_string($path))
        {
            throw new \InvalidArgumentException("Path is not a string");
        }

        if(!file_exists($path) || !is_readable($path))
        {
            throw new \RuntimeException("Couldn't read from path \"$path\"");
        }
    }
} 