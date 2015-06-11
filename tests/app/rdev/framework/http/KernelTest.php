<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the HTTP kernel
 */
namespace RDev\Framework\HTTP;
use Monolog\Logger;
use RDev\HTTP\Requests\Request;
use RDev\HTTP\Responses\Response;
use RDev\HTTP\Responses\ResponseHeaders;
use RDev\IoC\Container;
use RDev\Routing\Router;
use RDev\Routing\Compilers\Compiler;
use RDev\Routing\Compilers\Parsers\Parser;
use RDev\Routing\Dispatchers\Dispatcher;
use RDev\Tests\Applications\Mocks\MonologHandler;
use RDev\Tests\HTTP\Middleware\Mocks\HeaderSetter;
use RDev\Tests\Routing\Mocks\Controller;
use RDev\Tests\Routing\Mocks\ExceptionalRouter;

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
     * Tests disabling all middleware
     */
    public function testDisablingAllMiddleware()
    {
        $kernel = $this->getKernel(false);
        $kernel->addMiddleware("foo");
        $kernel->disableAllMiddleware();
        $this->assertEquals([], $kernel->getMiddleware());
    }

    /**
     * Tests disabling certain middleware
     */
    public function testDisablingCertainMiddleware()
    {
        $kernel = $this->getKernel(false);
        $kernel->addMiddleware("foo");
        $kernel->addMiddleware("bar");
        $kernel->onlyDisableMiddleware(["foo"]);
        $this->assertEquals(["bar"], $kernel->getMiddleware());
    }

    /**
     * Tests enabling certain middleware
     */
    public function testEnablingCertainMiddleware()
    {
        $kernel = $this->getKernel(false);
        $kernel->addMiddleware("foo");
        $kernel->addMiddleware("bar");
        $kernel->onlyEnableMiddleware(["foo"]);
        $this->assertEquals(["foo"], $kernel->getMiddleware());
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
        $this->assertInstanceOf(Response::class, $response);
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
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(ResponseHeaders::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Tests handling a request with middleware
     */
    public function testHandlingWithMiddleware()
    {
        $kernel = $this->getKernel(false);
        $kernel->addMiddleware(HeaderSetter::class);
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

        $router->any("/", Controller::class . "@noParameters");
        $logger = new Logger("kernelTest");
        $logger->pushHandler(new MonologHandler());

        return new Kernel($container, $router, $logger);
    }
}