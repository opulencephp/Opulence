<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the HTTP kernel
 */
namespace RDev\HTTP\Kernels;
use Monolog;
use RDev\HTTP\Requests;
use RDev\HTTP\Responses;
use RDev\HTTP\Routing;
use RDev\IoC;
use RDev\Pipelines;

class Kernel
{
    /** @var IoC\IContainer The dependency injection container */
    private $container = null;
    /** @var Routing\Router The router to use for requests */
    private $router = null;
    /** @var Monolog\Logger The logger to use */
    private $logger = null;
    /** @var array The list of global middleware */
    private $middleware = [];

    /**
     * @param IoC\IContainer $container The dependency injection container
     * @param Routing\Router $router The router to use
     * @param Monolog\Logger $logger The logger to use
     */
    public function __construct(IoC\IContainer $container, Routing\Router $router, Monolog\Logger $logger)
    {
        $this->container = $container;
        $this->router = $router;
        $this->logger = $logger;
    }

    /**
     * Adds middleware to the kernel
     *
     * @param string|array $middleware The middleware class or list of middleware classes to add
     */
    public function addMiddleware($middleware)
    {
        $this->middleware = array_merge($this->middleware, (array)$middleware);
    }

    /**
     * @return array
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * Handles an HTTP request
     *
     * @param Requests\Request $request The HTTP request to handle
     * @return Responses\Response The HTTP response
     */
    public function handle(Requests\Request $request)
    {
        try
        {
            $pipeline = new Pipelines\Pipeline($this->container, $this->middleware, "handle");

            return $pipeline->send($request, function($request)
            {
                return $this->router->route($request);
            });
        }
        catch(\Exception $ex)
        {
            $this->logger->addError("Failed to handle request: $ex");

            return new Responses\Response("", Responses\ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}