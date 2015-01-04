<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a YAML config reader
 */
namespace RDev\Configs\Readers;
use Symfony\Component\Yaml;
use Symfony\Component\Yaml\Exception;

class YAMLReader extends Reader
{
    /**
     * {@inheritdoc}
     */
    public function readFromFile($path, $configClassName = "RDev\\Configs\\Config")
    {
        switch($this->fileSystem->getExtension($path))
        {
            case "yml":
                try
                {
                    $decodedYAML = Yaml\Yaml::parse($this->fileSystem->read($path));

                    if(!is_array($decodedYAML))
                    {
                        $decodedYAML = [$decodedYAML];
                    }
                }
                catch(Exception\ParseException $ex)
                {
                    throw new \RuntimeException("Invalid YAML config file: " . $ex->getMessage());
                }

                return $this->createConfigFromArrayAndClassName($decodedYAML, $configClassName);
            default:
                throw new \RuntimeException("Invalid config file extension: " . $this->fileSystem->getExtension($path));
        }
    }

    /**
     * {@inheritdoc}
     * @param string $input The YAML string
     */
    public function readFromInput($input, $configClassName = "RDev\\Configs\\Config")
    {
        if(!is_string($input))
        {
            throw new \InvalidArgumentException("Input is not string");
        }

        try
        {
            $decodedYAML = Yaml\Yaml::parse($input);

            if(!is_array($decodedYAML))
            {
                $decodedYAML = [$decodedYAML];
            }
        }
        catch(Exception\ParseException $ex)
        {
            throw new \RuntimeException("Invalid YAML config file: " . $ex->getMessage());
        }

        return $this->createConfigFromArrayAndClassName($decodedYAML, $configClassName);
    }
} 