<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the router factory
 */
namespace RDev\Routing\Factories;
use RDev\HTTP;
use RDev\IoC;
use RDev\Routing;
use RDev\Routing\Configs;

class RouterFactory
{
    /**
     * Creates a router from a config
     *
     * @param Configs\RouterConfig $config The config to instantiate from
     * @param IoC\IContainer $container The IoC container to use
     * @return Routing\Router The instantiated router
     */
    public function createFromConfig(Configs\RouterConfig $config, IoC\IContainer $container)
    {
        $router = new Routing\Router($container, new Routing\Dispatcher($container), $config["compiler"]);
        $this->createRoutesFromConfigArray($router, $config->getArrayCopy());

        return $router;
    }

    /**
     * Creates all the nested group routes in a config array
     *
     * @param Routing\Router $router The router to create routes on
     * @param array $configArray The config to create routes from
     */
    private function createGroupedRoutesFromConfigArray(Routing\Router &$router, array $configArray)
    {
        if(isset($configArray["groups"]))
        {
            foreach($configArray["groups"] as $groupOptions)
            {
                $router->group($groupOptions["options"], function () use ($router, $groupOptions)
                {
                    if(isset($groupOptions["routes"]))
                    {
                        foreach($groupOptions["routes"] as $route)
                        {
                            $router->addRoute($route);
                        }
                    }

                    if(isset($groupOptions["groups"]))
                    {
                        $this->createGroupedRoutesFromConfigArray($router, $groupOptions);
                    }
                });
            }
        }
    }

    /**
     * Creates routes from a config
     *
     * @param Routing\Router $router The router to create routes on
     * @param array $configArray The config to create routes from
     *      This must be an array because we will need to recursively iterate through it
     */
    private function createRoutesFromConfigArray(Routing\Router &$router, array $configArray)
    {
        $this->createGroupedRoutesFromConfigArray($router, $configArray);

        if(isset($configArray["routes"]))
        {
            foreach($configArray["routes"] as $route)
            {
                $router->addRoute($route);
            }
        }
    }
} 