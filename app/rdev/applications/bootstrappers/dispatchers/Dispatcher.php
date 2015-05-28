<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the bootstrapper dispatcher
 */
namespace RDev\Applications\Bootstrappers\Dispatchers;
use RDev\Applications\Application;
use RDev\Applications\Bootstrappers\IBootstrapperRegistry;
use RDev\IoC\IContainer;
use RuntimeException;

class Dispatcher implements IDispatcher
{
    /** @var Application The application */
    private $application = null;
    /** @var IContainer The IoC container */
    private $container = null;
    /** @var bool Whether or not we force eager loading for all bootstrappers */
    private $forceEagerLoading = false;
    /** @var array The list of bootstrapper classes that have been run */
    private $runBootstrappers = [];

    /**
     * @param Application $application The application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
        $this->container = $this->application->getIoCContainer();
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(IBootstrapperRegistry $registry)
    {
        if($this->forceEagerLoading)
        {
            $eagerBootstrapperClasses = $registry->getEagerBootstrapperClasses();
            $lazyBootstrapperClasses = array_unique(array_values($registry->getBindingsToLazyBootstrapperClasses()));
            $bootstrapperClasses = array_merge($eagerBootstrapperClasses, $lazyBootstrapperClasses);
            $this->dispatchEagerly($registry, $bootstrapperClasses);
        }
        else
        {
            $this->dispatchEagerly($registry, $registry->getEagerBootstrapperClasses());
            $this->dispatchLazily($registry, $registry->getBindingsToLazyBootstrapperClasses());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function forceEagerLoading($doForce)
    {
        $this->forceEagerLoading = (bool)$doForce;
    }

    /**
     * Dispatches the registry eagerly
     *
     * @param IBootstrapperRegistry $registry The bootstrapper registry
     * @param array $bootstrapperClasses The list of bootstrapper classes to dispatch
     * @throws RuntimeException Thrown if there was a problem dispatching the bootstrappers
     */
    private function dispatchEagerly(IBootstrapperRegistry $registry, array $bootstrapperClasses)
    {
        $bootstrapperObjects = [];

        foreach($bootstrapperClasses as $bootstrapperClass)
        {
            $bootstrapper = $registry->getInstance($bootstrapperClass);
            $bootstrapper->registerBindings($this->container);
            $bootstrapperObjects[] = $bootstrapper;
        }

        foreach($bootstrapperObjects as $bootstrapper)
        {
            $this->container->call([$bootstrapper, "run"], [], true);
        }

        // Call the shutdown method
        $this->application->registerPreShutdownTask(function () use ($bootstrapperObjects)
        {
            foreach($bootstrapperObjects as $bootstrapper)
            {
                $this->container->call([$bootstrapper, "shutdown"], [], true);
            }
        });
    }

    /**
     * Dispatches the registry lazily
     *
     * @param IBootstrapperRegistry $registry The bootstrapper registry
     * @param array $bindingsToBootstrapperClasses The mapping of bindings to their bootstrapper classes
     * @throws RuntimeException Thrown if there was a problem dispatching the bootstrappers
     */
    private function dispatchLazily(IBootstrapperRegistry $registry, array $bindingsToBootstrapperClasses)
    {
        // This gets passed around by reference so that it'll have the latest objects come time to shut down
        $bootstrapperObjects = [];

        foreach($bindingsToBootstrapperClasses as $boundClass => $bootstrapperClass)
        {
            $this->container->bind(
                $boundClass,
                function () use ($registry, &$bootstrapperObjects, $boundClass, &$bootstrapperClass)
                {
                    $bootstrapper = $registry->getInstance($bootstrapperClass);
                    $bootstrapperObjects[] = $bootstrapper;

                    if(!isset($this->runBootstrappers[$bootstrapperClass]))
                    {
                        $bootstrapper->registerBindings($this->container);
                        $this->container->call([$bootstrapper, "run"], [], true);
                        $this->runBootstrappers[$bootstrapperClass] = true;
                    }

                    return $this->container->makeShared($boundClass);
                }
            );
        }

        // Call the shutdown method
        $this->application->registerPreShutdownTask(function () use (&$bootstrapperObjects)
        {
            foreach($bootstrapperObjects as $bootstrapper)
            {
                $this->container->call([$bootstrapper, "shutdown"], [], true);
            }
        });
    }
}