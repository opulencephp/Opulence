<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the console application for use in testing
 */
namespace RDev\Tests\Framework\Tests\Mocks;
use Monolog;
use RDev\Applications;
use RDev\Applications\Environments;
use RDev\Framework\Tests;
use RDev\IoC;
use RDev\Sessions;
use RDev\Tests\Applications\Mocks;

class ConsoleApplicationTestCase extends Tests\ConsoleApplicationTestCase
{
    /** @var array The list of bootstrapper classes to include */
    private static $bootstrappers = [
        "RDev\\Framework\\Bootstrappers\\HTTP\\Views\\Template",
        "RDev\\Framework\\Bootstrappers\\Console\\Commands\\Commands",
        "RDev\\Framework\\Bootstrappers\\Console\\Composer\\Composer",
    ];

    /**
     * {@inheritdoc}
     */
    protected function setApplication()
    {
        // Create and bind all of the components of our application
        $paths = new Applications\Paths([
            "configs" => __DIR__ . "/../configs"
        ]);
        $logger = new Monolog\Logger("application");
        $logger->pushHandler(new Mocks\MonologHandler());
        $environment = new Environments\Environment(Environments\Environment::TESTING);
        $container = new IoC\Container();
        $session = new Sessions\Session();
        $container->bind("RDev\\Applications\\Paths", $paths);
        $container->bind("Monolog\\Logger", $logger);
        $container->bind("RDev\\Applications\\Environments\\Environment", $environment);
        $container->bind("RDev\\IoC\\IContainer", $container);
        $container->bind("RDev\\Sessions\\ISession", $session);

        // Actually set the application
        $this->application = new Applications\Application(
            $paths,
            $logger,
            $environment,
            $container,
            $session
        );
        $this->application->registerBootstrappers(self::$bootstrappers);
    }
}