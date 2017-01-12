<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc;

/**
 * Defines a factory binding
 */
class FactoryBinding implements IBinding
{
    /** @var callable The factory */
    private $factory = null;
    /** @var bool Whether or not the factory should be resolved as a singleton */
    private $resolveAsSingleton = false;

    /**
     * @param callable $factory The factory
     * @param bool $resolveAsSingleton Whether or not the factory should be resolved as a singleton
     */
    public function __construct(callable $factory, bool $resolveAsSingleton)
    {
        $this->factory = $factory;
        $this->resolveAsSingleton = $resolveAsSingleton;
    }

    /**
     * @return callable
     */
    public function getFactory() : callable
    {
        return $this->factory;
    }

    /**
     * @return bool
     */
    public function resolveAsSingleton() : bool
    {
        return $this->resolveAsSingleton;
    }
}
