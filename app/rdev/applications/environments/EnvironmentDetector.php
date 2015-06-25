<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines methods for fetching details about the environment
 */
namespace RDev\Applications\Environments;
use RDev\Applications\Environments\Hosts\Host;
use RDev\Applications\Environments\Hosts\HostRegistry;

class EnvironmentDetector implements IEnvironmentDetector
{
    /**
     * {@inheritdoc}
     */
    public function detect(HostRegistry $hostRegistry)
    {
        $hostName = gethostname();

        foreach($hostRegistry->getHosts() as $environmentName => $hosts)
        {
            /** @var Host $host */
            foreach($hosts as $host)
            {
                if(($host->usesRegex() && preg_match($host->getHost(), $hostName) === 1) || $host->getHost() === $hostName)
                {
                    return $environmentName;
                }
            }
        }

        // Default to production
        return Environment::PRODUCTION;
    }
} 