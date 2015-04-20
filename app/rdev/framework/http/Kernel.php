<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the HTTP kernel
 */
namespace RDev\Framework\HTTP;
use Exception;
use Monolog\Logger;
use RDev\HTTP\Requests\Request;
use RDev\HTTP\Responses\Response;
use RDev\HTTP\Responses\ResponseHeaders;
use RDev\IoC\IContainer;
use RDev\Pipelines\Pipeline;
use RDev\Routing\Router;

class Kernel
{
    /** @var IContainer The dependency injection container */
    private $container = null;
    /** @var Router The router to use for requests */
    private $router = null;
    /** @var Logger The logger to use */
    private $logger = null;
    /** @var array The list of global middleware */
    private $middleware = [];

    /**
     * @param IContainer $container The dependency injection container
     * @param Router $router The router to use
     * @param Logger $logger The logger to use
     */
    public function __construct(IContainer $container, Router $router, Logger $logger)
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
     * @param Request $request The HTTP request to handle
     * @return Response The HTTP response
     */
    public function handle(Request $request)
    {
        try
        {
            $pipeline = new Pipeline($this->container, $this->middleware, "handle");

            return $pipeline->send($request, function($request)
            {
                return $this->router->route($request);
            });
        }
        catch(Exception $ex)
        {
            $this->logger->addError("Failed to handle request: $ex");

            return new Response("", ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}