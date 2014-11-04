<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the application config
 */
namespace RDev\Applications\Configs;
use RDev\Configs;
use RDev\IoC\Configs as IoCConfigs;
use RDev\Routing\Configs as RouterConfigs;
use RDev\Sessions;

class ApplicationConfig extends Configs\Config
{
    /**
     * {@inheritdoc}
     */
    public function exchangeArray($configArray)
    {
        if(!$this->isValid($configArray))
        {
            throw new \RuntimeException("Invalid application config");
        }

        $this->setUpIoCFromArray($configArray);
        $this->setUpEnvironmentFromArray($configArray);
        $this->setUpRouterFromArray($configArray);
        $this->setUpMonologFromArray($configArray);
        $this->setUpSessionFromArray($configArray);
        $this->setUpBootstrappersFromArray($configArray);
        $this->configArray = $configArray;
    }

    /**
     * Sets up the bootstrappers from a config array
     *
     * @param array $configArray The config array
     * @throws \RuntimeException Thrown if there was a problem with the bootstrapper config
     */
    private function setUpBootstrappersFromArray(array &$configArray)
    {
        if(!isset($configArray["bootstrappers"]))
        {
            $configArray["bootstrappers"] = [];
        }
    }

    /**
     * Sets up the environment from a config array
     *
     * @param array $configArray The config array
     * @throws \RuntimeException Thrown if there was a problem with the environment config
     */
    private function setUpEnvironmentFromArray(array &$configArray)
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
     * @throws \RuntimeException Thrown if there was a problem with the IoC config
     */
    private function setUpIoCFromArray(array &$configArray)
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
     * @throws \RuntimeException Thrown if there was a problem with the Monolog config
     */
    private function setUpMonologFromArray(array &$configArray)
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
     * @throws \RuntimeException Thrown if there was a problem with the router config
     */
    private function setUpRouterFromArray(array &$configArray)
    {
        if(!isset($configArray["routing"]))
        {
            $configArray["routing"] = [];
        }

        $configArray["routing"] = new RouterConfigs\RouterConfig($configArray["routing"]);
    }

    /**
     * Sets up the session from a config array
     *
     * @param array $configArray The config array
     * @throws \RuntimeException Thrown if there was a problem with the session config
     */
    private function setUpSessionFromArray(array &$configArray)
    {
        if(isset($configArray["session"]) && isset($configArray["session"]["session"]))
        {
            if(is_string($configArray["session"]["session"]))
            {
                if(!class_exists($configArray["session"]["session"]))
                {
                    throw new \RuntimeException("Class {$configArray['session']['session']} does not exist");
                }

                $configArray["session"]["session"] = new $configArray["session"]["session"]();
            }

            if(!$configArray["session"]["session"] instanceof Sessions\ISession)
            {
                throw new \RuntimeException("Session does not implement ISession");
            }
        }
        else
        {
            $configArray["session"] = [
                "session" => new Sessions\Session()
            ];
        }
    }
} 