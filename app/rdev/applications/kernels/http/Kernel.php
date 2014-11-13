<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Defines the HTTP kernel
 */
namespace RDev\Applications\Kernels\HTTP;
use RDev\HTTP;
use RDev\Routing;

class Kernel
{
    /** @var Routing\Router The router to use for requests */
    private $router = null;

    /**
     * @param Routing\Router $router The router to use
     */
    public function __construct(Routing\Router $router)
    {
        $this->router = $router;
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
            return new HTTP\Response("", HTTP\ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}