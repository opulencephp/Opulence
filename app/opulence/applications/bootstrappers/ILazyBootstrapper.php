<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for lazy bootstrappers to implement
 */
namespace Opulence\Applications\Bootstrappers;

interface ILazyBootstrapper
{
    /**
     * Gets the list of classes and interfaces bound by this bootstrapper to the IoC container
     *
     * @return array The list of bound classes
     */
    public function getBindings();
}