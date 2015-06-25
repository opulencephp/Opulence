<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the environment registry
 */
namespace RDev\Applications\Environments\Hosts;

class HostRegistry
{
    /** @var Host[] The environment names to hosts */
    private $environmentsToHosts = [];

    /**
     * Gets the mapping of environment names to the list of hosts in that environment
     *
     * @return array The list of hosts
     */
    public function getHosts()
    {
        return $this->environmentsToHosts;
    }

    /**
     * Registers a host for a particular environment name
     *
     * @param string $environmentName The name of the environment this host belongs to
     * @param Host|Host[] $host The host to add
     */
    public function registerHost($environmentName, $host)
    {
        if(!isset($this->environmentsToHosts[$environmentName]))
        {
            $this->environmentsToHosts[$environmentName] = [];
        }

        $hosts = $host;

        if(!is_array($hosts))
        {
            $hosts = [$host];
        }

        $this->environmentsToHosts[$environmentName] = array_merge($this->environmentsToHosts[$environmentName], $hosts);
    }
}