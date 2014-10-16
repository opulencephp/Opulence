<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the application factory
 */
namespace RDev\Models\Applications\Factories;
use Monolog;
use Monolog\Handler;
use RDev\Models\Applications;
use RDev\Models\Applications\Configs;
use RDev\Models\HTTP;
use RDev\Models\IoC;
use RDev\Models\Routing\Configs as RoutingConfigs;
use RDev\Models\Routing\Factories;

class ApplicationFactory
{
    /**
     * Creates an instance of an application from a config
     *
     * @param Configs\ApplicationConfig $config The config to instantiate from
     * @return Applications\Application The instantiated application
     */
    public function createFromConfig(Configs\ApplicationConfig $config)
    {
        $logger = new Monolog\Logger("application");

        foreach($config["monolog"]["handlers"] as $name => $handler)
        {
            $logger->pushHandler($handler);
        }

        $environment = (new Applications\EnvironmentFetcher())->getEnvironment($config["environment"]);
        $connection = new HTTP\Connection();
        /** @var IoC\IContainer $container */
        $container = $config["ioc"]["container"];
        $this->registerBindingsFromConfig($container, $config["ioc"]);

        if(is_array($config["routing"]))
        {
            $config["routing"] = new RoutingConfigs\RouterConfig($config["routing"]);
        }

        $routerFactory = new Factories\RouterFactory();
        $router = $routerFactory->createFromConfig($config["routing"], $container, $connection);
        $session = $config["session"];

        // Create some default bindings
        $container->bind("RDev\\Models\\HTTP\\Connection", $connection);
        $container->bind("RDev\\Models\\HTTP\\Request", $connection->getRequest());
        $container->bind("Monolog\\Logger", $logger);
        $container->bind("RDev\\Models\\Sessions\\ISession", $session);

        return new Applications\Application($logger, $environment, $connection, $container, $router, $session);
    }

    /**
     * Registers the bindings from the config
     *
     * @param IoC\IContainer $container The IoC container
     * @param IoC\Configs\IoCConfig $config The bindings config
     */
    private function registerBindingsFromConfig(IoC\IContainer &$container, IoC\Configs\IoCConfig $config)
    {
        foreach($config["universal"] as $component => $concreteClassName)
        {
            $container->bind($component, $concreteClassName);
        }

        foreach($config["targeted"] as $targetClassName => $targetedBindings)
        {
            foreach($targetedBindings as $component => $concreteClassName)
            {
                $container->bind($component, $concreteClassName, $targetClassName);
            }
        }
    }
} 