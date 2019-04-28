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

use Opulence\Ioc\Bootstrappers\Bootstrapper;

/**
 * Defines the inspector that can determine what bindings are registered in a bootstrapper
 */
final class BindingInspector
{
    /** @var BindingInspectionContainer The container that can determine bindings */
    private $inspectionContainer;

    /**
     * @param BindingInspectionContainer|null $inspectionContainer The container that can determine bindings
     */
    public function __construct(BindingInspectionContainer $inspectionContainer = null)
    {
        $this->inspectionContainer = $inspectionContainer ?? new BindingInspectionContainer();
    }

    /**
     * Finds the bindings that were registered in a list of bootstrappers
     *
     * @param Bootstrapper[] $bootstrappers The bootstrappers to inspect
     * @return InspectionBinding[] The list of bindings that were found
     */
    public function getBindings(array $bootstrappers): array
    {
        foreach ($bootstrappers as $bootstrapper) {
            $this->inspectionContainer->setBootstrapper($bootstrapper);
            $bootstrapper->registerBindings($this->inspectionContainer);
        }

        return $this->inspectionContainer->getBindings();
    }
}
