<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Defines the interface for environment detectors to implement
 */
namespace RDev\Applications\Environments;

interface IEnvironmentDetector
{
    /**
     * Gets the environment the server belongs to, eg "production"
     *
     * @param array|callable $config The list of environment names to rules or the callback that returns the name
     * @return string The environment the server belongs to
     */
    public function detect($config);
}