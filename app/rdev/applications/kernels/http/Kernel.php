<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the HTTP kernel
 */
namespace RDev\Applications\Kernels\HTTP;
use Monolog;
use RDev\HTTP;
use RDev\Routing;

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
     * @param HTTP\Request $request The HTTP request to handle
     * @return HTTP\Response The HTTP response
     */
    public function handle(HTTP\Request $request)
    {
        try
        {
            return $this->router->route($request);
        }
        catch(\Exception $ex)
        {
            $this->logger->addError("Failed to handle request: $ex");

            return new HTTP\Response("", HTTP\ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}