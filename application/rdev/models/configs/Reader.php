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
     * Gets whether or not the config is valid
     *
     * @param IConfig $config The config to validate
     * @return bool True if the config is valid, otherwise false
     */
    abstract public function validateConfig(IConfig $config);

    /**
     * Loads a config file and converts it to a keyed array
     *
     * @param string $configClassName The name of the class of the config to use
     * @param array|string $config Either the already-formed array or a string pointing to the location of a config file
     * @return IConfig The input config converted to a config class
     * @throws \RuntimeException Thrown if the input config is invalid
     */
    public function load($configClassName, $config)
    {
        /** @var IConfig $configClassName */
        $convertedConfig = $configClassName::fromArray($this->convertConfigToArray($config));

        if(!$this->validateConfig($convertedConfig))
        {
            throw new \RuntimeException("Invalid config");
        }

        return $convertedConfig;
    }

    /**
     * Converts the input config to a config array
     *
     * @param array|string $config Either the already-formed array or a string pointing to the location of a config file
     * @return array The converted config
     * @throws \RuntimeException Thrown if the config is not a string or an array or if the config file doesn't exist
     */
    protected function convertConfigToArray($config)
    {
        if(is_array($config))
        {
            return $config;
        }

        // We'll assume from here that the config parameter is really the path to the config file
        if(!is_string($config))
        {
            throw new \RuntimeException("ConnectionPoolConfig is neither a string nor an array");
        }

        if(!file_exists($config))
        {
            throw new \RuntimeException("Invalid config path: " . $config);
        }

        $configPathInfo = pathinfo($config);

        switch($configPathInfo["extension"])
        {
            case "json":
                return $this->convertJSONFile($config);
            default:
                throw new \RuntimeException("Invalid config file extension: " . $configPathInfo["extension"]);
        }
    }

    /**
     * Converts a JSON file's contents to a config array
     *
     * @param string $path The path to the JSON file
     * @return array The converted config
     * @throws \RuntimeException Thrown if the JSON file is invalid
     */
    protected function convertJSONFile($path)
    {
        $decodedJSON = json_decode(file_get_contents($path), true);

        if($decodedJSON === null)
        {
            throw new \RuntimeException("Invalid JSON config file");
        }

        return $decodedJSON;
    }

    /**
     * Gets whether or not the config has the required fields
     *
     * @param array $configArray The config array to validate
     * @param array $requiredFields The array of keys required by the config
     * @return bool True if the config has the required fields, otherwise false
     */
    protected function hasRequiredFields(array $configArray, array $requiredFields)
    {
        foreach($requiredFields as $key => $value)
        {
            if(!array_key_exists($key, $configArray))
            {
                return false;
            }

            if(is_array($value))
            {
                return $this->hasRequiredFields($configArray[$key], $requiredFields[$key]);
            }
        }

        return true;
    }
} 