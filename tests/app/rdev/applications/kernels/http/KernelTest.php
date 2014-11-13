<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Tests the HTTP kernel
 */
namespace RDev\Applications\Kernels\HTTP;
use RDev\HTTP;
use RDev\IoC;
use RDev\Routing;
use RDev\Tests\Routing\Mocks;

class KernelTest extends \PHPUnit_Framework_TestCase 
{
    /**
     * Tests handling a request
     */
    public function testHandlingRequest()
    {
        $kernel = $this->getKernel(false);
        $request = HTTP\Request::createFromGlobals();
        $response = $kernel->handle($request);
        $this->assertInstanceOf("RDev\\HTTP\\Response", $response);
        $this->assertEquals(HTTP\ResponseHeaders::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Tests handling an exceptional request
     */
    public function testHandlingExceptionalRequest()
    {
        $kernel = $this->getKernel(true);
        $request = HTTP\Request::createFromGlobals();
        $response = $kernel->handle($request);
        $this->assertInstanceOf("RDev\\HTTP\\Response", $response);
        $this->assertEquals(HTTP\ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /**
     * Gets a kernel to use in testing
     *
     * @param bool $shouldThrowException True if the router should throw an exception, otherwise false
     * @return Kernel The kernel
     */
    private function getKernel($shouldThrowException)
    {
        if($shouldThrowException)
        {
            $router = new Mocks\ExceptionalRouter(
                new Routing\Dispatcher(new IoC\Container()),
                new Routing\RouteCompiler()
            );
        }
        else
        {
            $router = new Routing\Router(new Routing\Dispatcher(new IoC\Container()), new Routing\RouteCompiler());
        }

        $router->any("/", ["controller" => "RDev\\Tests\\Routing\\Mocks\\Controller@noParameters"]);

        return new Kernel($router);
    }
}