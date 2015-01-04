<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for bootstrappers
 */
namespace RDev\Applications\Bootstrappers;
use RDev\Applications;

interface IBootstrapper
{
    /**
     * Runs the bootstrapper
     */
    public function run();
}