<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a config reader, which can convert and validate JSON config files
 */
namespace RDev\Models\Configs\Readers;
use RDev\Models\Configs;
use RDev\Models\Files;

abstract class Reader
{
    /** @var Files\FileSystem The file system to use to read/write to files */
    protected $fileSystem = null;

    public function __construct()
    {
        $this->fileSystem = new Files\FileSystem();
    }

    /**
     * Reads a config from a file
     *
     * @param string $path The path to the file's location
     * @param string $configClassName The name of the class that implements IConfig to save the input to
     * @return Configs\IConfig The config object from the file
     * @throws Files\FileSystemException Thrown if there was a problem reading from the file
     * @throws \InvalidArgumentException Thrown if the config class name doesn't point to a class that implements IConfig
     */
    abstract public function readFromFile($path, $configClassName = "RDev\\Models\\Configs\\Config");

    /**
     * Creates a config from input
     *
     * @param mixed $input The input to read from
     *      For example, this could be a PHP array, JSON, and XML string, etc
     * @param string $configClassName The name of the class that implements IConfig to save the input to
     * @return Configs\IConfig The config object from the input
     * @throws Files\FileSystemException Thrown if there was a problem decoding the input
     * @throws \InvalidArgumentException Thrown if the config class name doesn't point to a class that implements IConfig
     */
    abstract public function readFromInput($input, $configClassName = "RDev\\Models\\Configs\\Config");

    /**
     * Creates a config of the input type from a config array
     *
     * @param array $configArray The config array to create the config from
     * @param string $configClassName The fully-qualified name of the class that implements IConfig to save the config to
     * @return Configs\IConfig The config from the input array
     */
    protected function createConfigFromArrayAndClassName(array $configArray, $configClassName)
    {
        $config = new $configClassName();

        if(!$config instanceof Configs\IConfig)
        {
            throw new \InvalidArgumentException("The class \"$configClassName\" doesn't implement IConfig");
        }

        /** @var Configs\IConfig $config */
        $config->fromArray($configArray);

        return $config;
    }
} 