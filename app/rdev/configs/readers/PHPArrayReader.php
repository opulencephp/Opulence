<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a PHP array config reader
 */
namespace RDev\Configs\Readers;

class PHPArrayReader extends Reader
{
    /**
     * {@inheritdoc}
     */
    public function readFromFile($path, $configClassName = "RDev\\Configs\\Config")
    {
        switch($this->fileSystem->getExtension($path))
        {
            case "php":
                $fileContents = $this->fileSystem->read($path);

                // Notice the little hack inside eval() to compile inline PHP
                $array = @eval("?>" . $fileContents);

                if($array === false || !is_array($array))
                {
                    throw new \RuntimeException("Invalid PHP array config file");
                }

                /** @var array $array */

                return $this->createConfigFromArrayAndClassName($array, $configClassName);
            default:
                throw new \RuntimeException("Invalid config file extension: " . $this->fileSystem->getExtension($path));
        }
    }

    /**
     * {@inheritdoc}
     * @param array $input The PHP array
     */
    public function readFromInput($input, $configClassName = "RDev\\Configs\\Config")
    {
        if(!is_array($input))
        {
            throw new \InvalidArgumentException("Input is not array");
        }

        return $this->createConfigFromArrayAndClassName($input, $configClassName);
    }
} 