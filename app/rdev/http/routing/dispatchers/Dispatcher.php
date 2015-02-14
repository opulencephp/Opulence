<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Dispatches routes to the appropriate controllers
 */
namespace RDev\HTTP\Routing\Dispatchers;
use RDev\HTTP\Requests;
use RDev\HTTP\Responses;
use RDev\HTTP\Routing;
use RDev\HTTP\Routing\Routes;
use RDev\IoC;
use RDev\Pipelines;

class Dispatcher implements IDispatcher
{
    /** @var IoC\IContainer The dependency injection container */
    private $container = null;

    /**
     * @param IoC\IContainer $container The dependency injection container
     */
    public function __construct(IoC\IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(Routes\CompiledRoute $route, Requests\Request $request)
    {
        $pipeline = new Pipelines\Pipeline($this->container, $route->getMiddleware(), "handle");

        try
        {
            $response = $pipeline->send($request, function (Requests\Request $request) use ($route)
            {
                $controller = $this->createController($route->getControllerName(), $request);

                return $this->callController($controller, $route);
            });

            if($response === null)
            {
                // Nothing returned a value, so return a basic HTTP response
                return new Responses\Response();
            }

            return $response;
        }
        catch(Pipelines\PipelineException $ex)
        {
            throw new Routing\RouteException("Failed to dispatch route: " . $ex->getMessage());
        }
    }

    /**
     * Calls the method on the input controller
     *
     * @param Routing\Controller $controller The instance of the controller to call
     * @param Routes\CompiledRoute $route The route being dispatched
     * @return Responses\Response Returns the value from the controller method
     * @throws Routing\RouteException Thrown if the method could not be called on the controller
     */
    private function callController(Routing\Controller $controller, Routes\CompiledRoute $route)
    {
        $parameters = [];

        try
        {
            $reflection = new \ReflectionMethod($controller, $route->getControllerMethod());

            if($reflection->isPrivate())
            {
                throw new Routing\RouteException("Method {$route->getControllerMethod()} is private");
            }

            $pathVariables = $route->getPathVariables();

            // Match the route variables to the method parameters
            foreach($reflection->getParameters() as $parameter)
            {
                if(isset($pathVariables[$parameter->getName()]))
                {
                    // There is a value set in the route
                    $parameters[$parameter->getPosition()] = $pathVariables[$parameter->getName()];
                }
                elseif(($defaultValue = $route->getDefaultValue($parameter->getName())) !== null)
                {
                    // There was a default value set in the route
                    $parameters[$parameter->getPosition()] = $defaultValue;
                }
                elseif(!$parameter->isDefaultValueAvailable())
                {
                    // There is no value/default value for this variable
                    throw new Routing\RouteException(
                        "No value set for parameter {$parameter->getName()}"
                    );
                }
            }

            return call_user_func_array([$controller, "callMethod"], [$route->getControllerMethod(), $parameters]);
        }
        catch(\Exception $ex)
        {
            throw new Routing\RouteException(
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
     * @param Requests\Request $request The request that's being routed
     * @return Routing\Controller The instantiated controller
     * @throws Routing\RouteException Thrown if the controller could not be instantiated
     */
    private function createController($controllerName, Requests\Request $request)
    {
        if(!class_exists($controllerName))
        {
            throw new Routing\RouteException("Controller class $controllerName does not exist");
        }

        // Just in case the request hasn't already been bound, bind it
        // This allows us to use it when resolving the controller class
        if(!is_object($this->container->getBinding("RDev\\HTTP\\Request")))
        {
            $this->container->bind("RDev\\HTTP\\Request", $request);
        }

        $controller = $this->container->makeShared($controllerName);

        if(!$controller instanceof Routing\Controller)
        {
            throw new Routing\RouteException("Controller class $controllerName does not extend the base controller");
        }

        $controller->setRequest($request);

        return $controller;
    }
} 