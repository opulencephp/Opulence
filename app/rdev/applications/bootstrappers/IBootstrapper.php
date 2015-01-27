<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for bootstrappers
 */
namespace RDev\Applications\Bootstrappers;
use RDev\IoC;;

interface IBootstrapper
{
    /**
     * Registers bindings to the dependency injection container
     *
     * @param IoC\IContainer $container The container to register any bindings to
     */
    public function registerBindings(IoC\IContainer $container);

    /**
     * NOTE:  Because the following function accepts a variable number of parameters, we do not define it inside
     * the interface.
     *
     * public function run();
     */
}