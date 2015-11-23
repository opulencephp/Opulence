<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Bootstrappers;

use Opulence\Environments\Environment;
use RuntimeException;

/**
 * Defines the bootstrapper registry
 */
class BootstrapperRegistry implements IBootstrapperRegistry
{
    /** @var Environment The current environment */
    private $environment = null;
    /** @var Paths The application paths */
    private $paths = null;
    /** @var array The list of all bootstrapper classes in the application */
    private $allBootstrappers = [];
    /** @var array The list of deferred bootstrapper classes */
    private $bindingsToLazyBootstrapperClasses = [];
    /** @var array The list of expedited bootstrapper classes */
    private $eagerBootstrapperClasses = [];
    /** @var array The list of bootstrapper classes to their instances */
    private $instances = [];

    /**
     * @param Paths $paths The application paths
     * @param Environment $environment The current environment
     */
    public function __construct(Paths $paths, Environment $environment)
    {
        $this->paths = $paths;
        $this->environment = $environment;
    }

    /**
     * @inheritdoc
     */
    public function getEagerBootstrappers()
    {
        return $this->eagerBootstrapperClasses;
    }

    /**
     * @inheritdoc
     */
    public function getInstance($bootstrapperClass)
    {
        if (!isset($this->instances[$bootstrapperClass])) {
            $this->instances[$bootstrapperClass] = new $bootstrapperClass($this->paths, $this->environment);
        }

        $bootstrapper = $this->instances[$bootstrapperClass];

        if (!$bootstrapper instanceof Bootstrapper) {
            throw new RuntimeException("\"$bootstrapperClass\" does not extend Bootstrapper");
        }

        return $bootstrapper;
    }

    /**
     * @inheritdoc
     */
    public function getLazyBootstrapperBindings()
    {
        return $this->bindingsToLazyBootstrapperClasses;
    }

    /**
     * @inheritdoc
     */
    public function registerBootstrappers(array $bootstrapperClasses)
    {
        $this->allBootstrappers = array_merge($this->allBootstrappers, $bootstrapperClasses);
    }

    /**
     * @inheritdoc
     */
    public function registerEagerBootstrapper($eagerBootstrapperClasses)
    {
        $eagerBootstrapperClasses = (array)$eagerBootstrapperClasses;
        $this->eagerBootstrapperClasses = array_merge($this->eagerBootstrapperClasses, $eagerBootstrapperClasses);
    }

    /**
     * @inheritdoc
     */
    public function registerLazyBootstrapper($bindings, $lazyBootstrapperClass)
    {
        $bindings = (array)$bindings;

        foreach ($bindings as $boundClass) {
            $this->bindingsToLazyBootstrapperClasses[$boundClass] = $lazyBootstrapperClass;
        }
    }

    /**
     * @inheritdoc
     */
    public function setBootstrapperDetails()
    {
        foreach ($this->allBootstrappers as $bootstrapperClass) {
            $bootstrapper = $this->getInstance($bootstrapperClass);

            if ($bootstrapper instanceof ILazyBootstrapper) {
                $this->registerLazyBootstrapper($bootstrapper->getBindings(), $bootstrapperClass);
            } else {
                $this->registerEagerBootstrapper($bootstrapperClass);
            }
        }
    }
}