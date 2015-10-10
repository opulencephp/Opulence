<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the environment resolver
 */
namespace Opulence\Applications\Environments\Resolvers;

use Opulence\Applications\Environments\Environment;
use Opulence\Applications\Environments\Hosts\HostRegex;
use Opulence\Applications\Environments\Hosts\IHost;

class EnvironmentResolver implements IEnvironmentResolver
{
    /** @var array The environment names to hosts */
    private $environmentsToHosts = [];

    /**
     * @inheritdoc
     */
    public function registerHost($environmentName, $hosts)
    {
        if (!isset($this->environmentsToHosts[$environmentName])) {
            $this->environmentsToHosts[$environmentName] = [];
        }

        if (!is_array($hosts)) {
            $hosts = [$hosts];
        }

        foreach ($hosts as $host) {
            $this->environmentsToHosts[$environmentName][] = $host;
        }
    }

    /**
     * @inheritdoc
     */
    public function resolve($hostName)
    {
        foreach ($this->environmentsToHosts as $environmentName => $hosts) {
            /** @var IHost $host */
            foreach ($hosts as $host) {
                if ($host instanceof HostRegex) {
                    if (preg_match($host->getValue(), $hostName) === 1) {
                        return $environmentName;
                    }
                } elseif ($host->getValue() === $hostName) {
                    return $environmentName;
                }
            }
        }

        // Default to production
        return Environment::PRODUCTION;
    }
} 