<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for environment detectors to implement
 */
namespace RDev\Applications\Environments;
use RDev\Applications\Environments\Hosts\HostRegistry;

interface IEnvironmentDetector
{
    /**
     * Gets the environment the server belongs to, eg "production"
     *
     * @param HostRegistry $hostRegistry The registry to check against
     * @return string The environment the server belongs to
     */
    public function detect(HostRegistry $hostRegistry);
}