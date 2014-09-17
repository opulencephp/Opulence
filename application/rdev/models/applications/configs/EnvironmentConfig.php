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
    /**
     * {@inheritdoc}
     */
    public function fromArray(array $configArray)
    {
        if(!$this->isValid($configArray))
        {
            throw new \RuntimeException("Invalid config");
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
            if(isset($configArray[$environment]) && !$this->isMachineListValid($configArray[$environment]))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Validates a list of machines for an environment
     *
     * @param string|array $machineList The name of the machine or the list of machines
     * @return bool True if the list is valid, otherwise false
     */
    private function isMachineListValid($machineList)
    {
        if(!is_array($machineList))
        {
            $machineList = [$machineList];
        }

        foreach($machineList as $machine)
        {
            if(!is_string($machine))
            {
                return false;
            }
        }

        return true;
    }
} 