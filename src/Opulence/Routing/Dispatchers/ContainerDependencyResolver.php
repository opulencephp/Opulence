<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Routing\Dispatchers;

use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;

/**
 * Defines the dependency resolver that uses the IoC container
 */
class ContainerDependencyResolver implements IDependencyResolver
{
    /** @var IContainer The IoC container */
    private $container;

    /**
     * @param IContainer $container The IoC container
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function resolve(string $interface)
    {
        try {
            return $this->container->resolve($interface);
        } catch (IocException $ex) {
            throw new DependencyResolutionException("Could not resolve dependencies for $interface", 0, $ex);
        }
    }
}
