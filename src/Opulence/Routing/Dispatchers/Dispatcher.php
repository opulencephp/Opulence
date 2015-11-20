<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Routing\Dispatchers;

use Closure;
use Exception;
use Opulence\Http\HttpException;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Ioc\IContainer;
use Opulence\Pipelines\Pipeline;
use Opulence\Pipelines\PipelineException;
use Opulence\Routing\Controller;
use Opulence\Routing\RouteException;
use Opulence\Routing\Routes\CompiledRoute;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Factories\IViewFactory;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;

/**
 * Dispatches routes to the appropriate controllers
 */
class Dispatcher implements IDispatcher
{
    /** @var IContainer The dependency injection container */
    private $container = null;

    /**
     * @param IContainer $container The dependency injection container
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function dispatch(CompiledRoute $route, Request $request, &$controller = null)
    {
        $pipeline = new Pipeline($this->container, $route->getMiddleware(), "handle");

        try {
            $response = $pipeline->send($request, function (Request $request) use ($route, &$controller) {
                if ($route->usesClosure()) {
                    $controller = $route->getController();
                } else {
                    $controller = $this->createController($route->getControllerName(), $request);
                }

                return $this->callController($controller, $route);
            });

            if ($response === null) {
                // Nothing returned a value, so return a basic HTTP response
                return new Response();
            }

            return $response;
        } catch (PipelineException $ex) {
            throw new RouteException("Failed to dispatch route", 0, $ex);
        }
    }

    /**
     * Calls the method on the input controller
     *
     * @param Controller|Closure|mixed $controller The instance of the controller to call
     * @param CompiledRoute $route The route being dispatched
     * @return Response Returns the value from the controller method
     * @throws RouteException Thrown if the method could not be called on the controller
     * @throws HttpException Thrown if the controller threw an HttpException
     */
    private function callController($controller, CompiledRoute $route)
    {
        try {
            if (is_callable($controller)) {
                $reflection = new ReflectionFunction($controller);
                $parameters = $this->getResolvedControllerParameters(
                    $reflection->getParameters(),
                    $route->getPathVars(),
                    $route,
                    true
                );

                $response = call_user_func_array($controller, $parameters);
            } else {
                $reflection = new ReflectionMethod($controller, $route->getControllerMethod());
                $parameters = $this->getResolvedControllerParameters(
                    $reflection->getParameters(),
                    $route->getPathVars(),
                    $route,
                    false
                );

                if ($reflection->isPrivate()) {
                    throw new RouteException("Method {$route->getControllerMethod()} is private");
                }

                if ($controller instanceof Controller) {
                    $response = call_user_func_array(
                        [$controller, "callMethod"],
                        [$route->getControllerMethod(), $parameters]
                    );
                } else {
                    $response = call_user_func_array([$controller, $route->getControllerMethod()], $parameters);
                }
            }

            if (is_string($response)) {
                $response = new Response($response);
            }

            return $response;
        } catch (HttpException $ex) {
            // We don't want to catch these exceptions, but we want to catch all others
            throw $ex;
        } catch (Exception $ex) {
            throw new RouteException(
                sprintf(
                    "Reflection failed for %s: %s",
                    $route->usesClosure() ? "closure" : "{$route->getControllerName()}::{$route->getControllerMethod()}",
                    $ex
                ),
                0,
                $ex
            );
        }
    }

    /**
     * Creates an instance of the input controller
     *
     * @param string $controllerName The fully-qualified name of the controller class to instantiate
     * @param Request $request The request that's being routed
     * @return Controller|mixed The instantiated controller
     * @throws RouteException Thrown if the controller could not be instantiated
     */
    private function createController($controllerName, Request $request)
    {
        if (!class_exists($controllerName)) {
            throw new RouteException("Controller class $controllerName does not exist");
        }

        // Just in case the request hasn't already been bound, bind it
        // This allows us to use it when resolving the controller class
        if (!is_object($this->container->getBinding(Request::class))) {
            $this->container->bind(Request::class, $request);
        }

        $controller = $this->container->makeShared($controllerName);

        if ($controller instanceof Controller) {
            $controller->setRequest($request);

            if ($this->container->isBound(IViewFactory::class)) {
                $controller->setViewFactory($this->container->makeShared(IViewFactory::class));
            }

            if ($this->container->isBound(ICompiler::class)) {
                $controller->setViewCompiler($this->container->makeShared(ICompiler::class));
            }
        }

        return $controller;
    }

    /**
     * Gets the resolved parameters for a controller
     *
     * @param ReflectionParameter[] $reflectionParameters The reflection parameters
     * @param array $pathVars The route path variables
     * @param CompiledRoute $route The route whose parameters we're resolving
     * @param bool $acceptObjectParameters Whether or not we'll accept objects as parameters
     * @return array The mapping of parameter names to their resolved values
     * @throws RouteException Thrown if the parameters could not be resolved
     */
    private function getResolvedControllerParameters(
        array $reflectionParameters,
        array $pathVars,
        CompiledRoute $route,
        $acceptObjectParameters
    ) {
        $resolvedParameters = [];

        // Match the route variables to the method parameters
        foreach ($reflectionParameters as $parameter) {
            if ($acceptObjectParameters && $parameter->getClass() !== null) {
                $className = $parameter->getClass()->getName();
                $resolvedParameters[$parameter->getPosition()] = $this->container->makeShared($className);
            } elseif (isset($pathVars[$parameter->getName()])) {
                // There is a value set in the route
                $resolvedParameters[$parameter->getPosition()] = $pathVars[$parameter->getName()];
            } elseif (($defaultValue = $route->getDefaultValue($parameter->getName())) !== null) {
                // There was a default value set in the route
                $resolvedParameters[$parameter->getPosition()] = $defaultValue;
            } elseif (!$parameter->isDefaultValueAvailable()) {
                // There is no value/default value for this variable
                throw new RouteException(
                    "No value set for parameter {$parameter->getName()}"
                );
            }
        }

        return $resolvedParameters;
    }
} 