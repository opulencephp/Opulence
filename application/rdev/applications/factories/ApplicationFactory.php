<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the application factory
 */
namespace RDev\Applications\Factories;
use Monolog;
use Monolog\Handler;
use RDev\Applications;
use RDev\Applications\Configs;
use RDev\HTTP;
use RDev\IoC;
use RDev\Routing\Configs as RoutingConfigs;
use RDev\Routing\Factories;

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
        $router = $routerFactory->createFromConfig($config["routing"], $container);
        $session = $config["session"];

        // Register bootstrappers
        $application = new Applications\Application($logger, $environment, $connection, $container, $router, $session);
        $application->registerBootstrappers($config["bootstrappers"]);

        return $application;
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