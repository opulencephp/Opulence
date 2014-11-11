<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the environment config
 */
namespace RDev\Applications\Configs;
use RDev\Applications;
use RDev\Configs;

class EnvironmentConfig extends Configs\Config
{
    /** @var array The list of allowed host value types */
    public static $hostValueTypes = ["regex"];

    /**
     * {@inheritdoc}
     */
    public function exchangeArray($configArray)
    {
        if(!$this->isValid($configArray))
        {
            throw new \RuntimeException("Invalid environment config");
        }

        if(isset($configArray["detector"]))
        {
            if(is_string($configArray["detector"]))
            {

            }
        }

        // Convert any strings to arrays for each of the environments
        foreach([
                    Applications\Environment::PRODUCTION,
                    Applications\Environment::STAGING,
                    Applications\Environment::TESTING,
                    Applications\Environment::DEVELOPMENT
                ] as $environment)
        {
            if(isset($configArray["names"][$environment]) && is_string($configArray["names"][$environment]))
            {
                $configArray["names"][$environment] = [$configArray["names"][$environment]];
            }
        }

        $this->configArray = $configArray;
    }

    /**
     * {@inheritdoc}
     */
    protected function isValid(array $configArray)
    {
        if($configArray === [])
        {
            return true;
        }

        if(isset($configArray["names"]))
        {
            // Allow a function to be passed in
            if(
                count($configArray["names"]) == 1 &&
                isset($configArray["names"][0]) &&
                is_callable($configArray["names"][0])
            )
            {
                return true;
            }

            // Make sure each machine list is valid for each environment that's specified
            foreach([
                        Applications\Environment::PRODUCTION,
                        Applications\Environment::STAGING,
                        Applications\Environment::TESTING,
                        Applications\Environment::DEVELOPMENT
                    ] as $environment)
            {
                if(
                    isset($configArray["names"][$environment]) &&
                    !$this->hostListIsValid($configArray["names"][$environment])
                )
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Validates a list of hosts for an environment
     *
     * @param string|array $hostList The name of the machine or the list of machines
     * @return bool True if the list is valid, otherwise false
     */
    private function hostListIsValid($hostList)
    {
        if(!is_array($hostList))
        {
            $hostList = [$hostList];
        }

        foreach($hostList as $host)
        {
            if(is_array($host))
            {
                if(!isset($host["type"]) || !in_array($host["type"], self::$hostValueTypes) || !isset($host["value"]))
                {
                    return false;
                }
            }
            elseif(!is_string($host))
            {
                return false;
            }
        }

        return true;
    }
} 