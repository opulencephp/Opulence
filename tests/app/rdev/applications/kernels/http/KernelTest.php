<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Tests the HTTP kernel
 */
namespace RDev\Applications\Kernels\HTTP;
use Monolog;
use RDev\HTTP;
use RDev\IoC;
use RDev\Routing;
use RDev\Routing\Compilers;
use RDev\Routing\Compilers\Parsers;
use RDev\Routing\Dispatchers;
use RDev\Tests\Applications\Mocks as ApplicationMocks;
use RDev\Tests\Routing\Mocks as RoutingMocks;

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
        $routeCompiler = new Compilers\Compiler(new Parsers\Parser());

        if($shouldThrowException)
        {
            $router = new RoutingMocks\ExceptionalRouter(
                new Dispatchers\Dispatcher(new IoC\Container()),
                $routeCompiler
            );
        }
        else
        {
            $router = new Routing\Router(new Dispatchers\Dispatcher(new IoC\Container()), $routeCompiler);
        }

        $router->any("/", ["controller" => "RDev\\Tests\\Routing\\Mocks\\Controller@noParameters"]);
        $logger = new Monolog\Logger("kernelTest");
        $logger->pushHandler(new ApplicationMocks\MonologHandler());

        return new Kernel($router, $logger);
    }
}