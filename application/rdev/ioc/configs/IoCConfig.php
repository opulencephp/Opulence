<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the bindings config
 */
namespace RDev\IoC\Configs;
use RDev\Configs;
use RDev\IoC;

class IoCConfig extends Configs\Config
{
    /**
     * {@inheritdoc}
     */
    public function exchangeArray($configArray)
    {
        if(!$this->isValid($configArray))
        {
            throw new \RuntimeException("Invalid bindings config");
        }

        if($configArray == [])
        {
            // Setup a default bindings
            $configArray = [
                "container" => "RDev\\IoC\\Container",
                "universal" => [],
                "targeted" => []
            ];
        }

        if(!isset($configArray["container"]))
        {
            // Default to our container
            $configArray["container"] = new IoC\Container();
        }

        if(is_string($configArray["container"]))
        {
            if(!class_exists($configArray["container"]))
            {
                throw new \RuntimeException("Class {$configArray['container']} does not exist");
            }

            $configArray["container"] = new $configArray["container"];
        }

        if(!$configArray["container"] instanceof IoC\IContainer)
        {
            throw new \RuntimeException("Container does not implement IContainer");
        }

        if(!isset($configArray["universal"]))
        {
            $configArray["universal"] = [];
        }

        if(!isset($configArray["targeted"]))
        {
            $configArray["targeted"] = [];
        }

        $this->configArray = $configArray;
    }

    /**
     * {@inheritdoc}
     */
    protected function isValid(array $configArray)
    {
        if($configArray != [])
        {
            if(isset($configArray["container"])
                && !is_string($configArray["container"])
                && !$configArray["container"] instanceof IoC\IContainer
            )
            {
                return false;
            }

            if(isset($configArray["universal"]))
            {
                if(!is_array($configArray["universal"]))
                {
                    return false;
                }

                foreach($configArray["universal"] as $interfaceName => $concreteClassName)
                {
                    if(!is_string($interfaceName))
                    {
                        return false;
                    }
                }
            }

            if(isset($configArray["targeted"]))
            {
                if(!is_array($configArray["targeted"]))
                {
                    return false;
                }

                foreach($configArray["targeted"] as $targetClassName => $bindings)
                {
                    if(!is_array($bindings))
                    {
                        return false;
                    }

                    foreach($bindings as $interfaceName => $concreteClassName)
                    {
                        if(!is_string($interfaceName))
                        {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }
} 