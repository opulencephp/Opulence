<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Bootstrappers;

use Opulence\Ioc\IContainer;

/**
 * Defines a bootstrapper that eagerly registers all bindings
 */
final class EagerBootstrapperDispatcher implements IBootstrapperDispatcher
{
    /** @var IContainer The DI container */
    private $container;

    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function dispatch(array $bootstrappers): void
    {
        /** @var Bootstrapper[] $bootstrapper */
        foreach ($bootstrappers as $bootstrapper) {
            $bootstrapper->registerBindings($this->container);
        }
    }
}
