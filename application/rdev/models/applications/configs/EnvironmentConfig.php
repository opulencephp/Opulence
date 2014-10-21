<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the environment config
 */
namespace RDev\Models\Applications\Configs;
use RDev\Models\Applications;
use RDev\Models\Configs;

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

        // Convert any strings to arrays for each of the environments
        foreach([
                    Applications\Application::ENV_PRODUCTION,
                    Applications\Application::ENV_STAGING,
                    Applications\Application::ENV_TESTING,
                    Applications\Application::ENV_DEVELOPMENT
                ] as $environment)
        {
            if(isset($configArray[$environment]) && is_string($configArray[$environment]))
            {
                $configArray[$environment] = [$configArray[$environment]];
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

        // Allow a function to be passed in
        if(count($configArray) == 1 && isset($configArray[0]) && is_callable($configArray[0]))
        {
            return true;
        }

        // Make sure each machine list is valid for each environment that's specified
        foreach([
                    Applications\Application::ENV_PRODUCTION,
                    Applications\Application::ENV_STAGING,
                    Applications\Application::ENV_TESTING,
                    Applications\Application::ENV_DEVELOPMENT
                ] as $environment)
        {
            if(isset($configArray[$environment]) && !$this->hostListIsValid($configArray[$environment]))
            {
                return false;
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