<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Framework\Http;

use Exception;
use Opulence\Debug\Exceptions\Handlers\IExceptionHandler;
use Opulence\Framework\Debug\Exceptions\Handlers\Http\IExceptionRenderer;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Ioc\IContainer;
use Opulence\Pipelines\Pipeline;
use Opulence\Routing\Middleware\MiddlewareParameters;
use Opulence\Routing\Middleware\ParameterizedMiddleware;
use Opulence\Routing\Router;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Factories\IViewFactory;
use Throwable;

/**
 * Defines the HTTP kernel
 */
class Kernel
{
    /** @var IContainer The dependency injection container */
    private $container = null;
    /** @var Router The router to use for requests */
    private $router = null;
    /** @var IExceptionHandler The exception handler used by the kernel */
    private $exceptionHandler = null;
    /** @var IExceptionRenderer The exception renderer used by the kernel */
    private $exceptionRenderer = null;
    /** @var array The list of global middleware */
    private $middleware = [];
    /** @var bool Whether or not all middleware are disabled */
    private $middlewareAreDisabled = false;
    /** @var array The list of enabled middleware */
    private $enabledMiddleware = [];
    /** @var array The list of disabled middleware */
    private $disabledMiddleware = [];

    /**
     * @param IContainer $container The dependency injection container
     * @param Router $router The router to use
     * @param IExceptionHandler $exceptionHandler The exception handler used by the kernel
     * @param IExceptionRenderer $exceptionRenderer The exception renderer used by the kernel
     */
    public function __construct(
        IContainer $container,
        Router $router,
        IExceptionHandler $exceptionHandler,
        IExceptionRenderer $exceptionRenderer
    ) {
        $this->container = $container;
        $this->router = $router;
        $this->exceptionHandler = $exceptionHandler;
        $this->exceptionRenderer = $exceptionRenderer;
    }

    /**
     * Adds middleware to the kernel
     *
     * @param string|object|array $middleware The middleware object, class, or list of middleware classes to add
     */
    public function addMiddleware($middleware)
    {
        if (!is_array($middleware)) {
            $middleware = [$middleware];
        }

        $this->middleware = array_merge($this->middleware, $middleware);
    }

    /**
     * Disables all middleware
     */
    public function disableAllMiddleware()
    {
        $this->middlewareAreDisabled = true;
    }

    /**
     * @return array
     */
    public function getMiddleware() : array
    {
        if ($this->middlewareAreDisabled) {
            return [];
        }

        if (count($this->enabledMiddleware) > 0) {
            return $this->enabledMiddleware;
        }

        if (count($this->disabledMiddleware) > 0) {
            $enabledMiddleware = [];

            foreach ($this->middleware as $middleware) {
                if (is_string($middleware)) {
                    $middlewareClass = $middleware;
                } elseif ($middleware instanceof MiddlewareParameters) {
                    $middlewareClass = $middleware->getMiddlewareClassName();
                } else {
                    $middlewareClass = get_class($middleware);
                }

                if (!in_array($middlewareClass, $this->disabledMiddleware)) {
                    $enabledMiddleware[] = $middleware;
                }
            }

            return $enabledMiddleware;
        }

        return $this->middleware;
    }

    /**
     * Handles an HTTP request
     *
     * @param Request $request The HTTP request to handle
     * @return Response The HTTP response
     */
    public function handle(Request $request) : Response
    {
        try {
            return (new Pipeline)
                ->send($request)
                ->through($this->convertMiddlewareToPipelineStages($this->getMiddleware()), 'handle')
                ->then(function ($request) {
                    return $this->router->route($request);
                })
                ->execute();
        } catch (Exception $ex) {
            $this->setExceptionRendererVars($request);
            $this->exceptionHandler->handle($ex);

            return $this->exceptionRenderer->getResponse();
        } catch (Throwable $ex) {
            $this->setExceptionRendererVars($request);
            $this->exceptionHandler->handle($ex);

            return $this->exceptionRenderer->getResponse();
        }
    }

    /**
     * Disables the argument middleware
     *
     * @param array $middleware The list of middleware classes to disable
     */
    public function onlyDisableMiddleware(array $middleware)
    {
        $this->disabledMiddleware = $middleware;
    }

    /**
     * Enables only the argument middleware
     *
     * @param array $middleware The list of middleware classes to enable
     */
    public function onlyEnableMiddleware(array $middleware)
    {
        $this->enabledMiddleware = $middleware;
    }

    /**
     * Converts middleware to pipeline stages
     *
     * @param array $middleware The middleware to convert to pipeline stages
     * @return callable[] The list of pipeline stages
     */
    protected function convertMiddlewareToPipelineStages(array $middleware) : array
    {
        $stages = [];

        foreach ($middleware as $singleMiddleware) {
            if ($singleMiddleware instanceof MiddlewareParameters) {
                /** @var MiddlewareParameters $singleMiddleware */
                /** @var ParameterizedMiddleware $tempMiddleware */
                $tempMiddleware = $this->container->resolve($singleMiddleware->getMiddlewareClassName());
                $tempMiddleware->setParameters($singleMiddleware->getParameters());
                $singleMiddleware = $tempMiddleware;
            } elseif (is_string($singleMiddleware)) {
                $singleMiddleware = $this->container->resolve($singleMiddleware);
            }

            $stages[] = $singleMiddleware;
        }

        return $stages;
    }

    /**
     * Sets the variables in the exception renderer
     *
     * @param Request $request The current HTTP request
     */
    private function setExceptionRendererVars(Request $request)
    {
        $this->exceptionRenderer->setRequest($request);

        if ($this->container->hasBinding(ICompiler::class)) {
            $this->exceptionRenderer->setViewCompiler($this->container->resolve(ICompiler::class));
        }

        if ($this->container->hasBinding(IViewFactory::class)) {
            $this->exceptionRenderer->setViewFactory($this->container->resolve(IViewFactory::class));
        }
    }
}
