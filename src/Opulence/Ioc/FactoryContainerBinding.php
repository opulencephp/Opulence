<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc;

/**
 * Defines a factory container binding
 * @internal
 */
class FactoryContainerBinding implements IContainerBinding
{
    /** @var callable The factory */
    private $factory;
    /** @var bool Whether or not the factory should be resolved as a singleton */
    private $resolveAsSingleton;

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
    public function getFactory(): callable
    {
        return $this->factory;
    }

    /**
     * @return bool
     */
    public function resolveAsSingleton(): bool
    {
        return $this->resolveAsSingleton;
    }
}
