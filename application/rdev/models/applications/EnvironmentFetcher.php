<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines methods for fetching details about the environment
 */
namespace RDev\Models\Applications;
use RDev\Models\Applications\Configs;

class EnvironmentFetcher
{
    /**
     * Gets the environment the server belongs to, eg "production"
     *
     * @param Configs\EnvironmentConfig|array $config The config to use for the environment
     *      The following keys are optional:
     *          "production" => server name/regex, or list of server names/regexes in the production environment
     *          "staging" => server name/regex, or list of server names/regexes in the staging environment
     *          "testing" => server name/regex, or list of server names/regexes in the testing environment
     *          "development" => server name/regex, or list of server names/regexes in the development environment
     *      Alternatively, a callback may be passed in as the only item for customization
     *          It must return the environment the current server resides in
     *          This only works for PHPArray configs because callbacks cannot be passed in other types like JSON
     * @return string The environment the server belongs to
     */
    public function getEnvironment($config)
    {
        if(is_array($config))
        {
            $config = new Configs\EnvironmentConfig($config);
        }

        // Allow a callback
        if($config->count() == 1 && isset($config[0]) && $config[0] instanceof \Closure)
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
                if($host == $thisHost)
                {
                    return $environment;
                }

                if(preg_match("/^" . $host . "$/", $thisHost) === 1)
                {
                    return $environment;
                }
            }
        }

        // Default to production
        return Application::ENV_PRODUCTION;
    }
} 