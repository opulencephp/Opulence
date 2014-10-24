<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for bootstrappers
 */
namespace RDev\Models\Applications\Bootstrappers;
use RDev\Models\Applications;

interface IBootstrapper
{
    /**
     * Runs the bootstrapper
     */
    public function run();

    /**
     * Sets the application that is being bootstrapped
     *
     * @param Applications\Application $application The application that is being bootstrapped
     */
    public function setApplication(Applications\Application $application);
}