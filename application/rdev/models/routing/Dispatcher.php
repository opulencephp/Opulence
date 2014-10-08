<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Dispatches routes to the appropriate controllers
 */
namespace RDev\Models\Routing;
use RDev\Controllers;
use RDev\Models\HTTP;
use RDev\Models\IoC;

class Dispatcher
{
    /** @var IoC\IContainer The dependency injection container */
    private $iocContainer = null;
    /** @var callable[] The list of filters that can be used */
    private $filters = [];

    /**
     * @param IoC\IContainer $iocContainer The dependency injection container
     */
    public function __construct(IoC\IContainer $iocContainer)
    {
        $this->iocContainer = $iocContainer;
    }

    /**
     * Dispatches the input route
     *
     * @param Route $route The route to dispatch
     * @param array @routeVariables The array of route variable names to their values
     * @return mixed|null The response from the controller or pre/post filters if there was one, otherwise null
     * @throws Exceptions\RouteException Thrown if the method could not be called on the controller
     */
    public function dispatch(Route $route, array $routeVariables)
    {
        $controller = $this->createController($route->getControllerName());

        // Do our pre-filters
        if(($preFilterReturnValue = $this->executeFilters($route->getPreFilters())) !== null)
        {
            return $preFilterReturnValue;
        }

        // Call our controller
        if(($controllerResponse = $this->callController($controller, $route, $routeVariables)) !== null)
        {
            return $controllerResponse;
        }

        // Do our post-filters
        if(($postFilterReturnValue = $this->executeFilters($route->getPostFilters())) !== null)
        {
            return $postFilterReturnValue;
        }

        return null;
    }

    /**
     * Registers a filter function that can be used before/after a request
     *
     * @param string $name The name of the filter
     * @param callable $callback The callback that executes custom logic
     */
    public function registerFilter($name, callable $callback)
    {
        $this->filters[$name] = $callback;
    }

    /**
     * Calls the method on the input controller
     *
     * @param Controllers\Controller $controller The instance of the controller to call
     * @param Route $route The route being dispatched
     * @param array $routeVariables The list of route variable names to their values
     * @return mixed Returns the value from the controller method
     * @throws Exceptions\RouteException Thrown if the method could not be called on the controller
     */
    private function callController(Controllers\Controller $controller, Route $route, array $routeVariables)
    {
        $parameters = [];

        try
        {
            $reflection = new \ReflectionMethod($controller, $route->getControllerMethod());

            if($reflection->isPrivate())
            {
                throw new Exceptions\RouteException("Method {$route->getControllerMethod()} is private");
            }

            // Match the route variables to the method parameters
            foreach($reflection->getParameters() as $parameter)
            {
                if(isset($routeVariables[$parameter->getName()]))
                {
                    // There is a value set in the route
                    $parameters[$parameter->getPosition()] = $routeVariables[$parameter->getName()];
                }
                elseif(($defaultValue = $route->getDefaultValue($parameter->getName())) !== null)
                {
                    // There was a default value set in the route
                    $parameters[$parameter->getPosition()] = $defaultValue;
                }
                elseif(!$parameter->isDefaultValueAvailable())
                {
                    // There is no value/default value for this variable
                    throw new Exceptions\RouteException(
                        "No value set for parameter {$parameter->getName()}"
                    );
                }
            }

            return call_user_func_array([$controller, "callMethod"], [$route->getControllerMethod(), $parameters]);
        }
        catch(\ReflectionException $ex)
        {
            throw new Exceptions\RouteException(
                sprintf(
                    "Reflection failed for method %s in controller %s: %s",
                    $route->getControllerMethod(),
                    get_class($controller),
                    $ex
                )
            );
        }
    }

    /**
     * Creates an instance of the input controller
     *
     * @param string $controllerName The fully-qualified name of the controller class to instantiate
     * @return Controllers\Controller The instantiated controller
     * @throws Exceptions\RouteException Thrown if the controller could not be instantiated
     */
    private function createController($controllerName)
    {
        if(!class_exists($controllerName))
        {
            throw new Exceptions\RouteException("Controller class $controllerName does not exist");
        }

        $controller = $this->iocContainer->createSingleton($controllerName);

        if(!$controller instanceof Controllers\Controller)
        {
            throw new Exceptions\RouteException("Controller class $controllerName does not extend the base controller");
        }

        return $controller;
    }

    /**
     * Executes the list of filters
     *
     * @param array $filterNames The list of filter names to execute
     * @return mixed|null The response from any of the filters if they returned something, otherwise null
     * @throws Exceptions\RouteException Thrown if the filter does not exist
     */
    private function executeFilters(array $filterNames)
    {
        foreach($filterNames as $filterName)
        {
            if(!isset($this->filters[$filterName]))
            {
                throw new Exceptions\RouteException("Filter $filterName is not registered");
            }

            if(($filterReturnValue = $this->filters[$filterName]()) !== null)
            {
                return $filterReturnValue;
            }
        }

        return null;
    }
} 