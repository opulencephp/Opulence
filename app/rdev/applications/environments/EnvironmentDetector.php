<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines methods for fetching details about the environment
 */
namespace RDev\Applications\Environments;

class EnvironmentDetector implements IEnvironmentDetector
{
    /** @var array The environment names to hosts */
    private $environmentsToHosts = [];

    /**
     * {@inheritdoc}
     */
    public function detect()
    {
        $hostName = gethostname();

        foreach($this->environmentsToHosts as $environmentName => $hosts)
        {
            /** @var Host $host */
            foreach($hosts as $host)
            {
                if(($host->usesRegex() && preg_match($host->getName(), $hostName) === 1) || $host->getName() === $hostName)
                {
                    return $environmentName;
                }
            }
        }

        // Default to production
        return Environment::PRODUCTION;
    }

    /**
     * Registers a host for a particular environment name
     *
     * @param string $environmentName The name of the environment this host belongs to
     * @param Host|Host[] $hosts The host or hosts to add
     */
    public function registerHost($environmentName, $hosts)
    {
        if(!isset($this->environmentsToHosts[$environmentName]))
        {
            $this->environmentsToHosts[$environmentName] = [];
        }

        if(!is_array($hosts))
        {
            $hosts = [$hosts];
        }

        foreach($hosts as $host)
        {
            $this->environmentsToHosts[$environmentName][] = $host;
        }
    }
} 