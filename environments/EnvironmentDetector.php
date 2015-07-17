<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the environment detector
 */
namespace Opulence\Applications\Environments;
use Opulence\Applications\Environments\Hosts\HostRegex;
use Opulence\Applications\Environments\Hosts\IHost;

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
            /** @var IHost $host */
            foreach($hosts as $host)
            {
                if($host instanceof HostRegex)
                {
                    if(preg_match($host->getValue(), $hostName) === 1)
                    {
                        return $environmentName;
                    }
                }
                elseif($host->getValue() === $hostName)
                {
                    return $environmentName;
                }
            }
        }

        // Default to production
        return Environment::PRODUCTION;
    }

    /**
     * {@inheritdoc}
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