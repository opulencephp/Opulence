<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines methods for fetching details about the environment
 */
namespace RDev\Applications\Environments;

class EnvironmentDetector implements IEnvironmentDetector
{
    /**
     * {@inheritdoc}
     */
    public function detect($config)
    {
        // Allow a callback
        if(is_callable($config))
        {
            return call_user_func($config);
        }

        $thisHost = gethostname();
        $environments = [];

        // Include all the environments with set configs
        foreach([
                    Environment::PRODUCTION,
                    Environment::STAGING,
                    Environment::TESTING,
                    Environment::DEVELOPMENT
                ] as $name)
        {
            if(isset($config[$name]))
            {
                $environments[$name] = $config[$name];
            }
        }

        // Loop through all the environments and find the one that matches this server
        foreach($environments as $name => $hosts)
        {
            foreach((array)$hosts as $host)
            {
                if(is_string($host))
                {
                    if($host == $thisHost)
                    {
                        return $name;
                    }
                }
                elseif(is_array($host))
                {
                    switch($host["type"])
                    {
                        case "regex":
                            if(preg_match($host["value"], $thisHost) === 1)
                            {
                                return $name;
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