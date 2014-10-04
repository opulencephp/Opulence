<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the router config
 */
namespace RDev\Models\Routing\Configs;
use RDev\Models\Configs;
use RDev\Models\HTTP;
use RDev\Models\Routing;

class RouterConfig extends Configs\Config
{
    /** @var array The list of approved methods */
    private static $approvedMethods = [
        HTTP\Request::METHOD_DELETE,
        HTTP\Request::METHOD_GET,
        HTTP\Request::METHOD_POST,
        HTTP\Request::METHOD_PUT
    ];

    /**
     * {@inheritdoc}
     */
    public function fromArray(array $configArray)
    {
        if(!$this->isValid($configArray))
        {
            throw new \RuntimeException("Invalid config");
        }

        if(isset($configArray["compiler"]))
        {
            $compiler = $configArray["compiler"];

            if(is_string($compiler))
            {
                // We assume this is a custom compiler class
                if(!class_exists($compiler))
                {
                    throw new \RuntimeException("Invalid custom route compiler: " . $compiler);
                }

                $configArray["compiler"] = new $compiler();
            }

            if(!$configArray["compiler"] instanceof Routing\IRouteCompiler)
            {
                throw new \RuntimeException("Route compiler does not implement IRouteCompiler");
            }
        }
        else
        {
            $configArray["compiler"] = new Routing\RouteCompiler();
        }

        if(!isset($configArray["routes"]))
        {
            $configArray["routes"] = [];
        }

        foreach($configArray["routes"] as $index => $route)
        {
            if(is_array($route))
            {
                // Convert the filters to arrays
                if(isset($route["options"]["pre"]) && is_string($route["options"]["pre"]))
                {
                    $route["options"]["pre"] = [$route["options"]["pre"]];
                }

                if(isset($route["options"]["post"]) && is_string($route["options"]["post"]))
                {
                    $route["options"]["post"] = [$route["options"]["post"]];
                }

                $configArray["routes"][$index] = $this->getRouteFromConfig($route);
            }

            if(!$configArray["routes"][$index] instanceof Routing\Route)
            {
                throw new \RuntimeException("Route does not extend Route");
            }
        }

        $this->configArray = $configArray;
    }

    /**
     * {@inheritdoc}
     */
    protected function isValid(array $configArray)
    {
        if($configArray == [])
        {
            // An empty config is ok
            return true;
        }

        if(isset($configArray["compiler"]))
        {
            if(!is_string($configArray["compiler"]) && !$configArray["compiler"] instanceof Routing\IRouteCompiler)
            {
                return false;
            }
        }

        if(isset($configArray["routes"]))
        {
            foreach($configArray["routes"] as $route)
            {
                if(!$route instanceof Routing\Route && (!is_array($route) || !$this->routeIsValid($route)))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Gets a route from a config
     *
     * @param array $configArray The config
     * @return Routing\Route The route from the config
     */
    private function getRouteFromConfig(array $configArray)
    {
        if(!is_array($configArray["methods"]))
        {
            $configArray["methods"] = [$configArray["methods"]];
        }

        return new Routing\Route($configArray["methods"], $configArray["path"], $configArray["options"]);
    }

    /**
     * Gets whether or not a route is valid
     *
     * @param array $configArray The route config
     * @return bool True if the route config is valid, otherwise false
     */
    private function routeIsValid(array $configArray)
    {
        if(!isset($configArray["methods"]) || (!is_string($configArray["methods"]) && !is_array($configArray["methods"])))
        {
            return false;
        }

        $methods = $configArray["methods"];

        if(!is_array($methods))
        {
            $methods = [$methods];
        }

        foreach($methods as $method)
        {
            if(!in_array($method, self::$approvedMethods))
            {
                return false;
            }
        }

        if(!isset($configArray["path"]) || !is_string($configArray["path"]))
        {
            return false;
        }

        if(!isset($configArray["options"]) || !isset($configArray["options"]["controller"]))
        {
            return false;
        }

        $atCharPos = strpos($configArray["options"]["controller"], "@");

        // Check that there's an "@" somewhere in the middle of the controller
        if($atCharPos === false || $atCharPos === 0 || $atCharPos === strlen($configArray["options"]["controller"]) - 1)
        {
            return false;
        }

        if(isset($configArray["options"]["variables"]))
        {
            foreach($configArray["options"]["variables"] as $varName => $value)
            {
                if(!is_string($varName) || !is_string($value))
                {
                    return false;
                }
            }
        }

        return true;
    }
} 