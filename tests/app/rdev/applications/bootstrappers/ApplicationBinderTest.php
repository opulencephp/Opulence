<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the bootstrapper configurator
 */
namespace RDev\Applications\Bootstrappers;
use RDev\Applications\Bootstrappers\Caching\ICache;
use RDev\Applications\Bootstrappers\Dispatchers\IDispatcher as IBootstrapperDispatcher;
use RDev\Applications\Tasks\Dispatchers\IDispatcher as ITaskDispatcher;
use RDev\Applications\Tasks\TaskTypes;

class ApplicationBinderTest extends \PHPUnit_Framework_TestCase
{
    /** @var ApplicationBinder The application binder to use in tests */
    private $applicationBinder = null;
    /** @var ITaskDispatcher The task dispatcher */
    private $taskDispatcher = null;
    /** @var IBootstrapperDispatcher The bootstrapper dispatcher */
    private $bootstrapperDispatcher = null;
    /** @var ICache The bootstrapper cache */
    private $bootstrapperCache = null;
    /** @var IBootstrapperRegistry The registry of bootstrappers */
    private $bootstrapperRegistry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->taskDispatcher = $this->getMock(ITaskDispatcher::class);
        $this->bootstrapperCache = $this->getMock(ICache::class);
        $this->bootstrapperDispatcher = $this->getMock(IBootstrapperDispatcher::class);
        $this->bootstrapperRegistry = $this->getMock(IBootstrapperRegistry::class);
        $this->applicationBinder = new ApplicationBinder(
            $this->bootstrapperRegistry,
            $this->bootstrapperDispatcher,
            $this->bootstrapperCache,
            $this->taskDispatcher,
            ["GLOBAL"]
        );
    }

    /**
     * Tests that the force eager loading parameter is respected
     */
    public function testForceEagerLoadingIsRespected()
    {
        $this->bootstrapperDispatcher->expects($this->at(0))
            ->method("forceEagerLoading")
            ->with(true);
        $this->bootstrapperDispatcher->expects($this->at(1))
            ->method("forceEagerLoading")
            ->with(false);
        $this->applicationBinder->bindToApplication(["KERNEL"], true, true);
        $this->applicationBinder->bindToApplication(["KERNEL"], false, true);
    }

    /**
     * Tests that kernel bootstrappers are registered
     */
    public function testKernelBootstrappersAreRegistered()
    {
        $this->bootstrapperRegistry->expects($this->once())
            ->method("registerBootstrapperClasses")
            ->with(["KERNEL"]);
        $this->applicationBinder->bindToApplication(["KERNEL"], false, true);
    }

    /**
     * Tests that the pre-start task is registered
     */
    public function testPreStartTaskIsRegistered()
    {
        $this->taskDispatcher->expects($this->once())
            ->method("registerTask")
            ->with(TaskTypes::PRE_START);
        $this->applicationBinder->bindToApplication(["KERNEL"], false, false, "");
    }
}