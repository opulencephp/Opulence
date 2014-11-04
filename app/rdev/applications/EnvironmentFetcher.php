<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines methods for fetching details about the environment
 */
namespace RDev\Applications;
use RDev\Applications\Configs;

class EnvironmentFetcher
{
    /**
     * Gets the environment the server belongs to, eg "production"
     *
     * @param Configs\EnvironmentConfig $config The environment config
     * @return string The environment the server belongs to
     */
    public function getEnvironment(Configs\EnvironmentConfig $config)
    {
        // Allow a callback
        if($config->count() == 1 && isset($config[0]) && is_callable($config[0]))
        {
            return call_user_func($config[0]);
        }

        $thisHost = gethostname();
        $environments = [];

        // Include all the environments with set configs
        foreach([
                    Application::ENV_PRODUCTION,
                    Application::ENV_STAGING,
                    Application::ENV_TESTING,
                    Application::ENV_DEVELOPMENT
                ] as $environment)
        {
            if(isset($config[$environment]))
            {
                $environments[$environment] = $config[$environment];
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
        return Application::ENV_PRODUCTION;
    }
} 