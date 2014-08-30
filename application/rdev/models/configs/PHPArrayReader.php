<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a PHP array config reader
 */
namespace RDev\Models\Configs;

class PHPArrayReader extends Reader
{
    /**
     * {@inheritdoc}
     */
    public function readFromFile($path, $configClassName = "RDev\\Models\\Configs\\Config")
    {
        // We'll assume from here that the config parameter is really the path to the config file
        if(!is_string($path) || !file_exists($path))
        {
            throw new \RuntimeException("Couldn't read from path \"$path\"");
        }

        $pathInfo = pathinfo($path);

        switch($pathInfo["extension"])
        {
            case "php":
                $fileContents = file_get_contents($path);

                // Notice the little hack inside eval() to compile inline PHP
                $array = @eval("?>" . $fileContents);

                if($array === false || !is_array($array))
                {
                    throw new \RuntimeException("Invalid PHP array config file");
                }

                /** @var array $array */

                return $this->createConfigFromArrayAndClassName($array, $configClassName);
            default:
                throw new \RuntimeException("Invalid config file extension: " . $pathInfo["extension"]);
        }
    }

    /**
     * {@inheritdoc}
     * @param array $input The PHP array
     */
    public function readFromInput($input, $configClassName = "RDev\\Models\\Configs\\Config")
    {
        if(!is_array($input))
        {
            throw new \InvalidArgumentException("Input is not array");
        }

        return $this->createConfigFromArrayAndClassName($input, $configClassName);
    }
} 