<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the application config
 */
namespace RDev\Models\Applications\Configs;
use RDev\Models\Configs;
use RDev\Models\IoC\Configs as IoCConfigs;
use RDev\Models\Routing\Configs as RouterConfigs;

class ApplicationConfig extends Configs\Config
{
    /**
     * {@inheritdoc}
     */
    public function fromArray(array $configArray)
    {
        if(!$this->isValid($configArray))
        {
            throw new \RuntimeException("Invalid application config");
        }

        $this->setupIoCFromArray($configArray);
        $this->setupEnvironmentFromArray($configArray);
        $this->setupRouterFromArray($configArray);
        $this->setupMonologFromArray($configArray);
        $this->configArray = $configArray;
    }

    /**
     * Sets up the environment from a config array
     *
     * @param array $configArray The config array
     */
    private function setupEnvironmentFromArray(array &$configArray)
    {
        if(!isset($configArray["environment"]))
        {
            $configArray["environment"] = [];
        }

        $configArray["environment"] = new EnvironmentConfig($configArray["environment"]);
    }

    /**
     * Sets up the IoC from a config array
     *
     * @param array $configArray The config array
     */
    private function setupIoCFromArray(array &$configArray)
    {
        if(!isset($configArray["ioc"]))
        {
            $configArray["ioc"] = [];
        }

        $configArray["ioc"] = new IoCConfigs\IoCConfig($configArray["ioc"]);
    }

    /**
     * Sets up the Monolog from a config array
     *
     * @param array $configArray The config array
     */
    private function setupMonologFromArray(array &$configArray)
    {
        if(!isset($configArray["monolog"]))
        {
            $configArray["monolog"] = [];
        }

        $configArray["monolog"] = new MonologConfig($configArray["monolog"]);
    }

    /**
     * Sets up the router from a config array
     *
     * @param array $configArray The config array
     */
    private function setupRouterFromArray(array &$configArray)
    {
        if(!isset($configArray["routing"]))
        {
            $configArray["routing"] = [];
        }

        $configArray["routing"] = new RouterConfigs\RouterConfig($configArray["routing"]);
    }
} 