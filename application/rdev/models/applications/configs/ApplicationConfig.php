<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the application config
 */
namespace RDev\Models\Applications\Configs;
use RDev\Models\Configs;
use RDev\Models\IoC;

class ApplicationConfig extends Configs\Config
{
    /**
     * {@inheritdoc}
     */
    public function fromArray(array $configArray)
    {
        if(!$this->isValid($configArray))
        {
            throw new \RuntimeException("Invalid config");
        }

        if(!isset($configArray["environment"]))
        {
            $configArray["environment"] = [];
        }

        if(!isset($configArray["router"]))
        {
            $configArray["router"] = [];
        }

        if(isset($configArray["bindings"]))
        {
            if(!isset($configArray["bindings"]["container"]))
            {
                // Default to our container
                $configArray["bindings"]["container"] = new IoC\Container();
            }

            if(is_string($configArray["bindings"]["container"]))
            {
                if(!class_exists($configArray["bindings"]["container"]))
                {
                    throw new \RuntimeException("Class {$configArray['bindings']['container']} does not exist");
                }

                $configArray["bindings"]["container"] = new $configArray["bindings"]["container"];
            }

            if(!$configArray["bindings"]["container"] instanceof IoC\IContainer)
            {
                throw new \RuntimeException("Container does not implement IContainer");
            }
        }
        else
        {
            $configArray["bindings"] = [
                "container" => new IoC\Container(),
                "universal" => [],
                "targeted" => []
            ];
        }

        $this->configArray = $configArray;
    }

    /**
     * {@inheritdoc}
     */
    protected function isValid(array $configArray)
    {
        if(isset($configArray["bindings"]))
        {
            if(isset($configArray["bindings"]["container"])
                && !is_string($configArray["bindings"]["container"])
                && !$configArray["bindings"]["container"] instanceof IoC\IContainer
            )
            {
                return false;
            }

            if(isset($configArray["bindings"]["universal"]))
            {
                if(!is_array($configArray["bindings"]["universal"]))
                {
                    return false;
                }

                foreach($configArray["bindings"]["universal"] as $interfaceName => $concreteClassName)
                {
                    if(!is_string($interfaceName) || !is_string($concreteClassName))
                    {
                        return false;
                    }
                }
            }

            if(isset($configArray["bindings"]["targeted"]))
            {
                if(!is_array($configArray["bindings"]["targeted"]))
                {
                    return false;
                }

                foreach($configArray["bindings"]["targeted"] as $targetClassName => $bindings)
                {
                    if(!is_array($bindings))
                    {
                        return false;
                    }

                    foreach($bindings as $interfaceName => $concreteClassName)
                    {
                        if(!is_string($interfaceName) || !is_string($concreteClassName))
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