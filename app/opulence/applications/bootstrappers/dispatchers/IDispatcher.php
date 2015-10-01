<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for bootstrapper dispatchers to implement
 */
namespace Opulence\Applications\Bootstrappers\Dispatchers;

use Opulence\Applications\Bootstrappers\IBootstrapperRegistry;
use RuntimeException;

interface IDispatcher
{
    /**
     * Dispatches a registry
     *
     * @param IBootstrapperRegistry $registry The registry to dispatch
     * @throws RuntimeException Thrown if there was a problem dispatching the bootstrappers
     */
    public function dispatch(IBootstrapperRegistry $registry);

    /**
     * Sets whether or not we force eager loading for all bootstrappers
     *
     * @param bool $doForce Whether or not to force eager loading
     */
    public function forceEagerLoading($doForce);
}