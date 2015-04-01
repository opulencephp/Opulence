<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the HTTP kernel
 */
namespace RDev\HTTP\Kernels;
use Monolog\Logger;
use RDev\HTTP\Requests\Request;
use RDev\HTTP\Responses\ResponseHeaders;
use RDev\HTTP\Routing\Router;
use RDev\HTTP\Routing\Compilers\Compiler;
use RDev\HTTP\Routing\Compilers\Parsers\Parser;
use RDev\HTTP\Routing\Dispatchers\Dispatcher;
use RDev\IoC\Container;
use RDev\Tests\Applications\Mocks\MonologHandler;
use RDev\Tests\HTTP\Routing\Mocks\ExceptionalRouter;

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
        $request = Request::createFromGlobals();
        $response = $kernel->handle($request);
        $this->assertInstanceOf("RDev\\HTTP\\Responses\\Response", $response);
        $this->assertEquals(ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /**
     * Tests handling a request
     */
    public function testHandlingRequest()
    {
        $kernel = $this->getKernel(false);
        $request = Request::createFromGlobals();
        $response = $kernel->handle($request);
        $this->assertInstanceOf("RDev\\HTTP\\Responses\\Response", $response);
        $this->assertEquals(ResponseHeaders::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Tests handling a request with middleware
     */
    public function testHandlingWithMiddleware()
    {
        $kernel = $this->getKernel(false);
        $kernel->addMiddleware("RDev\\Tests\\HTTP\\Middleware\\Mocks\\HeaderSetter");
        $request = Request::createFromGlobals();
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
        $container = new Container();
        $routeCompiler = new Compiler(new Parser());

        if($shouldThrowException)
        {
            $router = new ExceptionalRouter(new Dispatcher($container), $routeCompiler);
        }
        else
        {
            $router = new Router(new Dispatcher($container), $routeCompiler);
        }

        $router->any("/", ["controller" => "RDev\\Tests\\HTTP\\Routing\\Mocks\\Controller@noParameters"]);
        $logger = new Logger("kernelTest");
        $logger->pushHandler(new MonologHandler());

        return new Kernel($container, $router, $logger);
    }
}