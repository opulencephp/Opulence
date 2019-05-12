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
use Opulence\Ioc\ResolutionException;

/**
 * Defines the inspector that can determine what bindings are registered in a bootstrapper
 */
final class BindingInspector
{
    /** @var BindingInspectionContainer The container that can determine bindings */
    private $container;

    /**
     * @param BindingInspectionContainer|null $inspectionContainer The container that can determine bindings
     */
    public function __construct(BindingInspectionContainer $inspectionContainer = null)
    {
        $this->container = $inspectionContainer ?? new BindingInspectionContainer();
    }

    /**
     * Finds the bindings that were registered in a list of bootstrappers
     *
     * @param Bootstrapper[] $bootstrappers The bootstrappers to inspect
     * @return BootstrapperBinding[] The list of bindings that were found
     * @throws ImpossibleBindingException Thrown if the bindings are not possible to resolve
     */
    public function getBindings(array $bootstrappers): array
    {
        $failedInterfacesToBootstrappers = [];

        foreach ($bootstrappers as $bootstrapper) {
            try {
                $this->inspectBootstrapper($bootstrapper);
            } catch (ResolutionException $ex) {
                self::addFailedResolutionToMap($failedInterfacesToBootstrappers, $ex->getInterface(), $bootstrapper);
            }

            $this->retryFailedBootstrappers($failedInterfacesToBootstrappers);
        }

        if (\count($failedInterfacesToBootstrappers) > 0) {
            throw new ImpossibleBindingException($failedInterfacesToBootstrappers);
        }

        return $this->container->getBindings();
    }

    /**
     * Adds a failed resolution to a map
     *
     * @param array $failedInterfacesToBootstrappers The map to add to
     * @param string $interface The interface that could not be resolved
     * @param Bootstrapper $bootstrapper The bootstrapper where the resolution failed
     */
    private static function addFailedResolutionToMap(
        array &$failedInterfacesToBootstrappers,
        string $interface,
        Bootstrapper $bootstrapper
    ): void {
        if (!isset($failedInterfacesToBootstrappers[$interface])) {
            $failedInterfaces[$interface] = [];
        }

        $failedInterfacesToBootstrappers[$interface][] = $bootstrapper;
    }

    /**
     * Inspects an individual bootstrapper for bindings
     *
     * @param Bootstrapper $bootstrapper The bootstrapper to inspect
     * @throws ResolutionException Thrown if there was an error resolving any dependencies
     */
    private function inspectBootstrapper(Bootstrapper $bootstrapper): void
    {
        $this->container->setBootstrapper($bootstrapper);
        $bootstrapper->registerBindings($this->container);
    }

    /**
     * Retries any failed resolutions
     *
     * @param array $failedInterfacesToBootstrappers The map of failed resolutions to retry
     */
    private function retryFailedBootstrappers(array &$failedInterfacesToBootstrappers): void
    {
        foreach ($failedInterfacesToBootstrappers as $interface => $bootstrappers) {
            if (!$this->container->hasBinding($interface)) {
                // No point in retrying if the container still cannot resolve the interface
                continue;
            }

            foreach ($bootstrappers as $i => $bootstrapper) {
                try {
                    $this->inspectBootstrapper($bootstrapper);
                    // The bootstrapper must have been able to resolve everything, so remove it
                    unset($failedInterfacesToBootstrappers[$interface][$i]);

                    // If this interface doesn't have any more failed bootstrappers, remove it
                    if (\count($failedInterfacesToBootstrappers[$interface]) === 0) {
                        unset($failedInterfacesToBootstrappers[$interface]);
                    }
                } catch (ResolutionException $ex) {
                    self::addFailedResolutionToMap(
                        $failedInterfacesToBootstrappers,
                        $ex->getInterface(),
                        $bootstrapper
                    );
                }
            }
        }
    }
}
