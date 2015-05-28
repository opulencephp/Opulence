<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the console application for use in testing
 */
namespace RDev\Tests\Framework\Tests\Console\Mocks;
use Monolog\Logger;
use RDev\Applications\Application;
use RDev\Applications\Bootstrappers\BootstrapperRegistry;
use RDev\Applications\Bootstrappers\Dispatchers\Dispatcher;
use RDev\Applications\Environments\Environment;
use RDev\Applications\Paths;
use RDev\Framework\Tests\Console\ApplicationTestCase as BaseApplicationTestCase;
use RDev\IoC\Container;
use RDev\Sessions\Session;
use RDev\Tests\Applications\Mocks\MonologHandler;

class ApplicationTestCase extends BaseApplicationTestCase
{
    /** @var array The list of bootstrapper classes to include */
    private static $bootstrappers = [
        "RDev\\Framework\\Bootstrappers\\Console\\Commands\\Commands",
        "RDev\\Framework\\Bootstrappers\\Console\\Composer\\Composer",
    ];

    /**
     * {@inheritdoc}
     */
    protected function setApplication()
    {
        // Create and bind all of the components of our application
        $paths = new Paths([
            "configs" => __DIR__ . "/../../configs"
        ]);
        $logger = new Logger("application");
        $logger->pushHandler(new MonologHandler());
        $environment = new Environment(Environment::TESTING);
        $container = new Container();
        $session = new Session();
        $container->bind("RDev\\Applications\\Paths", $paths);
        $container->bind("Monolog\\Logger", $logger);
        $container->bind("RDev\\Applications\\Environments\\Environment", $environment);
        $container->bind("RDev\\IoC\\IContainer", $container);
        $container->bind("RDev\\Sessions\\ISession", $session);
        $this->application = new Application($paths, $logger, $environment, $container, $session);

        // Setup the bootstrappers
        $bootstrapperRegistry = new BootstrapperRegistry($paths, $environment);
        $bootstrapperDispatcher = new Dispatcher($this->application);
        $bootstrapperRegistry->registerEagerBootstrapper(self::$bootstrappers);
        $this->application->registerPreStartTask(function () use ($bootstrapperDispatcher, $bootstrapperRegistry)
        {
            $bootstrapperDispatcher->dispatch($bootstrapperRegistry);
        });
    }
}