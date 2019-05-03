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
use Opulence\Ioc\Bootstrappers\Inspection\Caching\IInspectionBindingCache;
use Opulence\Ioc\IContainer;

final class InspectionBindingBootstrapperDispatcher implements IBootstrapperDispatcher
{
    /** @var IInspectionBindingCache The cache to save inspection bindings with */
    private $inspectionBindingCache;
    /** @var LazyBindingRegistrant The registrant for our lazy bindings */
    private $lazyBindingRegistrant;
    /** @var BindingInspector The binding inspector to use */
    private $bindingInspector;

    /**
     * @param IContainer $container The container to use when dispatching bootstrappers
     * @param IInspectionBindingCache $inspectionBindingCache The cache to use for inspection bindings
     * @param BindingInspector|null $bindingInspector The binding inspector to use, or null if using the default
     */
    public function __construct(
        IContainer $container,
        IInspectionBindingCache $inspectionBindingCache,
        BindingInspector $bindingInspector = null
    ) {
        $this->inspectionBindingCache = $inspectionBindingCache;
        $this->bindingInspector = $bindingInspector ?? new BindingInspector();
        $this->lazyBindingRegistrant = new LazyBindingRegistrant($container);
    }

    /**
     * @inheritdoc
     */
    public function dispatch(array $bootstrappers): void
    {
        if (($inspectionBindings = $this->inspectionBindingCache->get()) === null) {
            $inspectionBindings = $this->bindingInspector->getBindings($bootstrappers);
            $this->inspectionBindingCache->set($inspectionBindings);
        }

        $this->lazyBindingRegistrant->registerBindings($inspectionBindings);
    }
}
