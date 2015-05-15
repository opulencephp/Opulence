<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Mocks the HTTP application for use in testing
 */
namespace RDev\Tests\Framework\Tests\HTTP\Mocks;
use Monolog\Logger;
use RDev\Applications\Application;
use RDev\Applications\Paths;
use RDev\Applications\Environments\Environment;
use RDev\Framework\Tests\HTTP\ApplicationTestCase as BaseApplicationTestCase;
use RDev\IoC\Container;
use RDev\Tests\Applications\Mocks\MonologHandler;

class ApplicationTestCase extends BaseApplicationTestCase
{
    /** @var array The list of bootstrapper classes to include */
    private static $bootstrappers = [
        "RDev\\Framework\\Bootstrappers\\HTTP\\Requests\\Request",
        "RDev\\Framework\\Bootstrappers\\HTTP\\Routing\\Router",
        "RDev\\Framework\\Bootstrappers\\HTTP\\Views\\TemplateFunctions"
    ];

    /**
     * {@inheritdoc}
     */
    protected function getGlobalMiddleware()
    {
        return [];
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
        $logger = new Logger("application");
        $logger->pushHandler(new MonologHandler());
        $environment = new Environment(Environment::TESTING);
        $container = new Container();
        $container->bind("RDev\\Applications\\Paths", $paths);
        $container->bind("Monolog\\Logger", $logger);
        $container->bind("RDev\\Applications\\Environments\\Environment", $environment);
        $container->bind("RDev\\IoC\\IContainer", $container);

        // Actually set the application
        $this->application = new Application($paths, $logger, $environment, $container);
        $this->application->registerBootstrappers(self::$bootstrappers);
    }
}