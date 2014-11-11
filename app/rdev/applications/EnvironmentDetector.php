<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines methods for fetching details about the environment
 */
namespace RDev\Applications;
use RDev\Applications\Configs;

class EnvironmentDetector implements IEnvironmentDetector
{
    /** @var Configs\EnvironmentConfig The config to use */
    private $config = null;

    /**
     * @param Configs\EnvironmentConfig $config The config to use
     */
    public function __construct(Configs\EnvironmentConfig $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function detect()
    {
        // Allow a callback
        if(
            isset($this->config["names"]) &&
            count($this->config["names"]) == 1 &&
            isset($this->config["names"][0]) &&
            is_callable($this->config["names"][0])
        )
        {
            return call_user_func($this->config["names"][0]);
        }

        $thisHost = gethostname();
        $environments = [];

        // Include all the environments with set configs
        foreach([
                    Environment::PRODUCTION,
                    Environment::STAGING,
                    Environment::TESTING,
                    Environment::DEVELOPMENT
                ] as $environment)
        {
            if(isset($this->config["names"][$environment]))
            {
                $environments[$environment] = $this->config["names"][$environment];
            }
        }

        // Loop through all the environments and find the one that matches this server
        foreach($environments as $environment => $hosts)
        {
            foreach($hosts as $host)
            {
                if(is_string($host))
                {
                    if($host == $thisHost)
                    {
                        return $environment;
                    }
                }
                elseif(is_array($host))
                {
                    switch($host["type"])
                    {
                        case "regex":
                            if(preg_match($host["value"], $thisHost) === 1)
                            {
                                return $environment;
                            }

                            break;
                    }
                }
            }
        }

        // Default to production
        return Environment::PRODUCTION;
    }
} 