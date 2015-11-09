<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Bootstrappers;

use Opulence\Applications\Tasks\Dispatchers\IDispatcher as ITaskDispatcher;
use Opulence\Applications\Tasks\TaskTypes;
use Opulence\Bootstrappers\Caching\ICache;
use Opulence\Bootstrappers\Dispatchers\IDispatcher as IBootstrapperDispatcher;

/**
 * Defines the class that binds the bootstrapper library to the application
 */
class ApplicationBinder
{
    /** @var BootstrapperRegistry The registry of bootstrappers */
    private $bootstrapperRegistry = null;
    /** @var IBootstrapperDispatcher The bootstrapper dispatcher */
    private $bootstrapperDispatcher = null;
    /** @var ITaskDispatcher The task dispatcher */
    private $taskDispatcher = null;
    /** @var ICache The bootstrapper cache */
    private $bootstrapperCache = null;
    /** @var array The list of global bootstrapper classes */
    private $globalBootstrapperClasses = [];

    /**
     * @param IBootstrapperRegistry $bootstrapperRegistry The registry of bootstrappers
     * @param IBootstrapperDispatcher $bootstrapperDispatcher The bootstrapper dispatcher
     * @param ICache $bootstrapperCache The bootstrapper cache
     * @param ITaskDispatcher $taskDispatcher The task dispatcher
     * @param array $globalBootstrapperClasses The list of global bootstrapper classes
     */
    public function __construct(
        IBootstrapperRegistry $bootstrapperRegistry,
        IBootstrapperDispatcher $bootstrapperDispatcher,
        ICache $bootstrapperCache,
        ITaskDispatcher $taskDispatcher,
        array $globalBootstrapperClasses
    ) {
        $this->bootstrapperRegistry = $bootstrapperRegistry;
        $this->bootstrapperDispatcher = $bootstrapperDispatcher;
        $this->bootstrapperCache = $bootstrapperCache;
        $this->taskDispatcher = $taskDispatcher;
        $this->globalBootstrapperClasses = $globalBootstrapperClasses;

        // Global bootstrappers should always be registered first
        $this->bootstrapperRegistry->registerBootstrappers($this->globalBootstrapperClasses);
    }

    /**
     * Configures the bootstrappers with the application
     *
     * @param array $kernelBootstrapperClasses The list of kernel-specific bootstrapper classes
     * @param bool $forceEagerLoading Whether or not to force all bootstrappers to use eager loading
     * @param bool $useCache Whether or not to cache bootstrapper settings
     * @param string $cachedRegistryFilePath The location of the bootstrapper registry cache file
     */
    public function bindToApplication(
        array $kernelBootstrapperClasses,
        $forceEagerLoading,
        $useCache,
        $cachedRegistryFilePath = ""
    ) {
        $this->bootstrapperDispatcher->forceEagerLoading($forceEagerLoading);
        $this->bootstrapperRegistry->registerBootstrappers($kernelBootstrapperClasses);

        // Register the task to dispatch the bootstrappers
        $this->taskDispatcher->registerTask(
            TaskTypes::PRE_START,
            function () use ($useCache, $cachedRegistryFilePath) {
                if ($useCache && !empty($cachedRegistryFilePath)) {
                    $this->bootstrapperCache->get($cachedRegistryFilePath, $this->bootstrapperRegistry);
                } else {
                    $this->bootstrapperRegistry->setBootstrapperDetails();
                }

                $this->bootstrapperDispatcher->dispatch($this->bootstrapperRegistry);
            }
        );
    }
}