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

class Kernel
{
    /** @var Routing\Router The router to use for requests */
    private $router = null;
    /** @var Monolog\Logger The logger to use */
    private $logger = null;

    /**
     * @param Routing\Router $router The router to use
     * @param Monolog\Logger $logger The logger to use
     */
    public function __construct(Routing\Router $router, Monolog\Logger $logger)
    {
        $this->router = $router;
        $this->logger = $logger;
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
            return $this->router->route($request);
        }
        catch(\Exception $ex)
        {
            $this->logger->addError("Failed to handle request: $ex");

            return new Responses\Response("", Responses\ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}