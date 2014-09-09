<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a JSON config reader
 */
namespace RDev\Models\Configs;

class JSONReader extends Reader
{
    /**
     * {@inheritdoc}
     */
    public function readFromFile($path, $configClassName = "RDev\\Models\\Configs\\Config")
    {
        switch($this->fileSystem->getExtension($path))
        {
            case "json":
                $decodedJSON = json_decode($this->fileSystem->read($path), true);

                if($decodedJSON === null)
                {
                    throw new \RuntimeException("Invalid JSON config file");
                }

                return $this->createConfigFromArrayAndClassName($decodedJSON, $configClassName);
            default:
                throw new \RuntimeException("Invalid config file extension: " . $this->fileSystem->getExtension($path));
        }
    }

    /**
     * {@inheritdoc}
     * @param string $input The JSON string
     */
    public function readFromInput($input, $configClassName = "RDev\\Models\\Configs\\Config")
    {
        if(!is_string($input))
        {
            throw new \InvalidArgumentException("Input is not string");
        }

        $decodedJSON = json_decode($input, true);

        if($decodedJSON === null)
        {
            throw new \RuntimeException("Invalid JSON input");
        }

        return $this->createConfigFromArrayAndClassName($decodedJSON, $configClassName);
    }
} 