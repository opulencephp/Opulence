<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Dispatchers;

use Closure;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Controller;
use Opulence\Routing\Middleware\IMiddleware;
use Opulence\Routing\Middleware\MiddlewareParameters;
use Opulence\Routing\Middleware\ParameterizedMiddleware;
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
class RouteDispatcher implements IRouteDispatcher
{
    /** @var IDependencyResolver The dependency resolver */
    private $dependencyResolver = null;
    /** @var IMiddlewarePipeline The middleware pipeline */
    private $middlewarePipeline = null;

    /**
     * @param IDependencyResolver $dependencyResolver The dependency resolver
     * @param IMiddlewarePipeline $middlewarePipeline The middleware pipeline
     */
    public function __construct(IDependencyResolver $dependencyResolver, IMiddlewarePipeline $middlewarePipeline)
    {
        $this->dependencyResolver = $dependencyResolver;
        $this->middlewarePipeline = $middlewarePipeline;
    }

    /**
     * @inheritdoc
     */
    public function dispatch(CompiledRoute $route, Request $request, &$controller = null) : Response
    {
        $resolvedMiddleware = $this->resolveMiddleware($route->getMiddleware());
        $controllerCallable = function (Request $request) use ($route, &$controller) {
            if ($route->usesCallable()) {
                $controller = $route->getController();
            } else {
                $controller = $this->resolveController($route->getControllerName(), $request);
            }

            return $this->callController($controller, $route);
        };

        return $this->middlewarePipeline->send($request, $resolvedMiddleware, $controllerCallable);
    }

    /**
     * Calls the method on the input controller
     *
     * @param Controller|Closure|mixed $controller The instance of the controller to call
     * @param CompiledRoute $route The route being dispatched
     * @return Response Returns the value from the controller method
     * @throws RouteException Thrown if the method could not be called on the controller
     */
    private function callController($controller, CompiledRoute $route) : Response
    {
        if (is_callable($controller)) {
            try {
                $reflection = new ReflectionFunction($controller);
            } catch (\ReflectionException $e) {
                throw new RouteException("Function {$controller} does not exist");
            }
            $parameters = $this->resolveControllerParameters(
                $reflection->getParameters(),
                $route->getPathVars(),
                $route,
                true
            );

            $response = $controller(...$parameters);
        } else {
            try {
                $reflection = new ReflectionMethod($controller, $route->getControllerMethod());
            } catch (\ReflectionException $e) {
                throw new RouteException("Method {$route->getControllerMethod()} does not exist");
            }
            $parameters = $this->resolveControllerParameters(
                $reflection->getParameters(),
                $route->getPathVars(),
                $route,
                false
            );

            if ($reflection->isPrivate()) {
                throw new RouteException("Method {$route->getControllerMethod()} is private");
            }

            if ($controller instanceof Controller) {
                $response = $controller->callMethod(
                    $route->getControllerMethod(), $parameters
                );
            } else {
                $response = $controller->{$route->getControllerMethod()}(...$parameters);
            }
        }

        if (is_string($response)) {
            $response = new Response($response);
        }

        return $response;
    }

    /**
     * Creates an instance of the input controller
     *
     * @param string $controllerName The fully-qualified name of the controller class to instantiate
     * @param Request $request The request that's being routed
     * @return Controller|mixed The instantiated controller
     * @throws RouteException Thrown if the controller could not be instantiated
     */
    private function resolveController(string $controllerName, Request $request)
    {
        if (!class_exists($controllerName)) {
            throw new RouteException("Controller class $controllerName does not exist");
        }

        $controller = $this->dependencyResolver->resolve($controllerName);

        if ($controller instanceof Controller) {
            $controller->setRequest($request);

            try {
                $controller->setViewFactory(
                    $this->dependencyResolver->resolve(IViewFactory::class)
                );
            } catch (DependencyResolutionException $ex) {
                // Don't do anything
            }

            try {
                $controller->setViewCompiler(
                    $this->dependencyResolver->resolve(ICompiler::class)
                );
            } catch (DependencyResolutionException $ex) {
                // Don't do anything
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
    private function resolveControllerParameters(
        array $reflectionParameters,
        array $pathVars,
        CompiledRoute $route,
        bool $acceptObjectParameters
    ) : array {
        $resolvedParameters = [];

        // Match the route variables to the method parameters
        foreach ($reflectionParameters as $parameter) {
            if ($acceptObjectParameters && $parameter->getClass() !== null) {
                $className = $parameter->getClass()->getName();
                $resolvedParameters[$parameter->getPosition()] = $this->dependencyResolver->resolve($className);
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

    /**
     * Resolves the list of middleware
     *
     * @param array $middleware The middleware to resolve
     * @return IMiddleware[] The list of resolved middleware
     */
    private function resolveMiddleware(array $middleware) : array
    {
        $resolvedMiddleware = [];

        foreach ($middleware as $singleMiddleware) {
            if ($singleMiddleware instanceof MiddlewareParameters) {
                /** @var MiddlewareParameters $singleMiddleware */
                /** @var ParameterizedMiddleware $tempMiddleware */
                $tempMiddleware = $this->dependencyResolver->resolve($singleMiddleware->getMiddlewareClassName());
                $tempMiddleware->setParameters($singleMiddleware->getParameters());
                $singleMiddleware = $tempMiddleware;
            } elseif (is_string($singleMiddleware)) {
                $singleMiddleware = $this->dependencyResolver->resolve($singleMiddleware);
            }

            $resolvedMiddleware[] = $singleMiddleware;
        }

        return $resolvedMiddleware;
    }
}
