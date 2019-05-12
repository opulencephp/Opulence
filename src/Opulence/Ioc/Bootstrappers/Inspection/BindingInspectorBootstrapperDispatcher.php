<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Bootstrappers\Inspection;

use Opulence\Ioc\Bootstrappers\IBootstrapperDispatcher;
use Opulence\Ioc\Bootstrappers\Inspection\Caching\IBootstrapperBindingCache;
use Opulence\Ioc\IContainer;

/**
 * Defines a bootstrapper dispatcher that uses binding inspection
 */
final class BindingInspectorBootstrapperDispatcher implements IBootstrapperDispatcher
{
    /** @var IBootstrapperBindingCache|null The cache to save bootstrapper bindings with, or null if not caching */
    private $bootstrapperBindingCache;
    /** @var LazyBindingRegistrant The registrant for our lazy bindings */
    private $lazyBindingRegistrant;
    /** @var BindingInspector The binding inspector to use */
    private $bindingInspector;

    /**
     * @param IContainer $container The container to use when dispatching bootstrappers
     * @param IBootstrapperBindingCache|null $bootstrapperBindingCache The cache to use for bootstrapper bindings, or null if not caching
     * @param BindingInspector|null $bindingInspector The binding inspector to use, or null if using the default
     */
    public function __construct(
        IContainer $container,
        IBootstrapperBindingCache $bootstrapperBindingCache = null,
        BindingInspector $bindingInspector = null
    ) {
        $this->bootstrapperBindingCache = $bootstrapperBindingCache;
        $this->bindingInspector = $bindingInspector ?? new BindingInspector();
        $this->lazyBindingRegistrant = new LazyBindingRegistrant($container);
    }

    /**
     * @inheritdoc
     */
    public function dispatch(array $bootstrappers): void
    {
        if ($this->bootstrapperBindingCache === null) {
            $bootstrapperBindings = $this->bindingInspector->getBindings($bootstrappers);
        } elseif (($bootstrapperBindings = $this->bootstrapperBindingCache->get()) === null) {
            $bootstrapperBindings = $this->bindingInspector->getBindings($bootstrappers);
            $this->bootstrapperBindingCache->set($bootstrapperBindings);
        }

        $this->lazyBindingRegistrant->registerBindings($bootstrapperBindings);
    }
}
