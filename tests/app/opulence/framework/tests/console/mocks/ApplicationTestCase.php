<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the console application for use in testing
 */
namespace Opulence\Tests\Framework\Tests\Console\Mocks;
use Monolog\Logger;
use Opulence\Applications\Application;
use Opulence\Applications\Bootstrappers\BootstrapperRegistry;
use Opulence\Applications\Bootstrappers\Dispatchers\Dispatcher;
use Opulence\Applications\Environments\Environment;
use Opulence\Applications\Paths;
use Opulence\Applications\Tasks\Dispatchers\Dispatcher as TaskDispatcher;
use Opulence\Applications\Tasks\TaskTypes;
use Opulence\Framework\Bootstrappers\Console\Commands\Commands;
use Opulence\Framework\Bootstrappers\Console\Composer\Composer;
use Opulence\Framework\Tests\Console\ApplicationTestCase as BaseApplicationTestCase;
use Opulence\IoC\Container;
use Opulence\IoC\IContainer;
use Opulence\Tests\Applications\Mocks\MonologHandler;

class ApplicationTestCase extends BaseApplicationTestCase
{
    /** @var array The list of bootstrapper classes to include */
    private static $bootstrappers = [
        Commands::class,
        Composer::class,
    ];

    /**
     * {@inheritdoc}
     */
    protected function getKernelLogger()
    {
        $logger = new Logger("application");
        $logger->pushHandler(new MonologHandler());

        return $logger;
    }

    /**
     * {@inheritdoc}
     */
    protected function setApplication()
    {
        // Create and bind all of the components of our application
        $paths = new Paths([
            "configs" => __DIR__ . "/../../configs"
        ]);
        $taskDispatcher = new TaskDispatcher();
        // Purposely set this to a weird value so we can test that it gets overwritten with the "test" environment
        $environment = new Environment("foo");
        $container = new Container();
        $container->bind(Paths::class, $paths);
        $container->bind(TaskDispatcher::class, $taskDispatcher);
        $container->bind(Environment::class, $environment);
        $container->bind(IContainer::class, $container);
        $this->application = new Application($paths, $taskDispatcher, $environment, $container);

        // Setup the bootstrappers
        $bootstrapperRegistry = new BootstrapperRegistry($paths, $environment);
        $bootstrapperDispatcher = new Dispatcher($taskDispatcher, $container);
        $bootstrapperRegistry->registerEagerBootstrapper(self::$bootstrappers);
        $taskDispatcher->registerTask(TaskTypes::PRE_START, function () use ($bootstrapperDispatcher, $bootstrapperRegistry)
        {
            $bootstrapperDispatcher->dispatch($bootstrapperRegistry);
        });
    }
}