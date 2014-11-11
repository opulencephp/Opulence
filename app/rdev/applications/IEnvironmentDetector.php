<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Defines the interface for environment detectors to implement
 */
namespace RDev\Applications;

interface IEnvironmentDetector
{
    /**
     * Gets the environment the server belongs to, eg "production"
     *
     * @return string The environment the server belongs to
     */
    public function detect();
}