<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the HTTP kernel
 */
namespace RDev\HTTP\Kernels;
use Monolog;
use RDev\HTTP\Requests;
use RDev\HTTP\Responses;
use RDev\HTTP\Routing;
use RDev\HTTP\Routing\Compilers;
use RDev\HTTP\Routing\Compilers\Parsers;
use RDev\HTTP\Routing\Dispatchers;
use RDev\IoC;
use RDev\Tests\Applications\Mocks as ApplicationMocks;
use RDev\Tests\HTTP\Routing\Mocks as RoutingMocks;

class KernelTest extends \PHPUnit_Framework_TestCase 
{
    /**
     * Tests adding empty middleware
     */
    public function testAddingEmptyMiddleware()
    {
        $kernel = $this->getKernel(false);
        $kernel->addMiddleware([]);
        $this->assertEquals([], $kernel->getMiddleware());
    }

    /**
     * Tests adding middleware
     */
    public function testAddingMiddleware()
    {
        $kernel = $this->getKernel(false);
        // Test a single middleware
        $kernel->addMiddleware("foo");
        $this->assertEquals(["foo"], $kernel->getMiddleware());
        // Test multiple middleware
        $kernel->addMiddleware(["bar", "baz"]);
        $this->assertEquals(["foo", "bar", "baz"], $kernel->getMiddleware());
    }

    /**
     * Tests getting middleware
     */
    public function testGettingMiddleware()
    {
        $kernel = $this->getKernel(false);
        $this->assertEquals([], $kernel->getMiddleware());
        $kernel->addMiddleware("foo");
        $this->assertEquals(["foo"], $kernel->getMiddleware());
    }

    /**
     * Tests handling an exceptional request
     */
    public function testHandlingExceptionalRequest()
    {
        $kernel = $this->getKernel(true);
        $request = Requests\Request::createFromGlobals();
        $response = $kernel->handle($request);
        $this->assertInstanceOf("RDev\\HTTP\\Responses\\Response", $response);
        $this->assertEquals(Responses\ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /**
     * Tests handling a request
     */
    public function testHandlingRequest()
    {
        $kernel = $this->getKernel(false);
        $request = Requests\Request::createFromGlobals();
        $response = $kernel->handle($request);
        $this->assertInstanceOf("RDev\\HTTP\\Responses\\Response", $response);
        $this->assertEquals(Responses\ResponseHeaders::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Tests handling a request with middleware
     */
    public function testHandlingWithMiddleware()
    {
        $kernel = $this->getKernel(false);
        $kernel->addMiddleware("RDev\\Tests\\HTTP\\Middleware\\Mocks\\HeaderSetter");
        $request = Requests\Request::createFromGlobals();
        $response = $kernel->handle($request);
        $this->assertEquals("bar", $response->getHeaders()->get("foo"));
    }

    /**
     * Gets a kernel to use in testing
     *
     * @param bool $shouldThrowException True if the router should throw an exception, otherwise false
     * @return Kernel The kernel
     */
    private function getKernel($shouldThrowException)
    {
        $container = new IoC\Container();
        $routeCompiler = new Compilers\Compiler(new Parsers\Parser());

        if($shouldThrowException)
        {
            $router = new RoutingMocks\ExceptionalRouter(
                new Dispatchers\Dispatcher($container),
                $routeCompiler
            );
        }
        else
        {
            $router = new Routing\Router(new Dispatchers\Dispatcher($container), $routeCompiler);
        }

        $router->any("/", ["controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters"]);
        $logger = new Monolog\Logger("kernelTest");
        $logger->pushHandler(new ApplicationMocks\MonologHandler());

        return new Kernel($container, $router, $logger);
    }
}