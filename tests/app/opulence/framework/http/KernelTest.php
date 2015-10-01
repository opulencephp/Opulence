<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the HTTP kernel
 */
namespace Opulence\Framework\HTTP;

use Monolog\Logger;
use Opulence\HTTP\Requests\Request;
use Opulence\HTTP\Responses\Response;
use Opulence\HTTP\Responses\ResponseHeaders;
use Opulence\IoC\Container;
use Opulence\Routing\Dispatchers\Dispatcher;
use Opulence\Routing\Router;
use Opulence\Routing\Routes\Compilers\ICompiler;
use Opulence\Routing\Routes\Compilers\Parsers\IParser;
use Opulence\Routing\Routes\CompiledRoute;
use Opulence\Routing\Routes\ParsedRoute;
use Opulence\Tests\Applications\Mocks\MonologHandler;
use Opulence\Tests\HTTP\Middleware\Mocks\HeaderSetter;
use Opulence\Tests\Routing\Mocks\Controller;
use Opulence\Tests\Routing\Mocks\ExceptionalRouter;

class KernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding empty middleware
     */
    public function testAddingEmptyMiddleware()
    {
        $kernel = $this->getKernel(Request::METHOD_GET, false);
        $kernel->addMiddleware([]);
        $this->assertEquals([], $kernel->getMiddleware());
    }

    /**
     * Tests adding middleware
     */
    public function testAddingMiddleware()
    {
        $kernel = $this->getKernel(Request::METHOD_GET, false);
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
        $kernel = $this->getKernel(Request::METHOD_GET, false);
        $kernel->addMiddleware("foo");
        $kernel->disableAllMiddleware();
        $this->assertEquals([], $kernel->getMiddleware());
    }

    /**
     * Tests disabling certain middleware
     */
    public function testDisablingCertainMiddleware()
    {
        $kernel = $this->getKernel(Request::METHOD_GET, false);
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
        $kernel = $this->getKernel(Request::METHOD_GET, false);
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
        $kernel = $this->getKernel(Request::METHOD_GET, false);
        $this->assertEquals([], $kernel->getMiddleware());
        $kernel->addMiddleware("foo");
        $this->assertEquals(["foo"], $kernel->getMiddleware());
    }

    /**
     * Tests handling an exceptional request
     */
    public function testHandlingExceptionalRequest()
    {
        $kernel = $this->getKernel(Request::METHOD_GET, true);
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
        $kernel = $this->getKernel(Request::METHOD_GET, false);
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
        $kernel = $this->getKernel(Request::METHOD_GET, false);
        $kernel->addMiddleware(HeaderSetter::class);
        $request = Request::createFromGlobals();
        $response = $kernel->handle($request);
        $this->assertEquals("bar", $response->getHeaders()->get("foo"));
    }

    /**
     * Gets a kernel to use in testing
     *
     * @param string $method The HTTP method the routes are valid for
     * @param bool $shouldThrowException True if the router should throw an exception, otherwise false
     * @return Kernel The kernel
     */
    private function getKernel($method, $shouldThrowException)
    {
        $container = new Container();
        $compiledRoute = $this->getMock(CompiledRoute::class, [], [], "", false);
        $compiledRoute->expects($this->any())->method("isMatch")->willReturn(true);
        $compiledRoute->expects($this->any())->method("getControllerName")->willReturn(Controller::class);
        $compiledRoute->expects($this->any())->method("getControllerMethod")->willReturn("noParameters");
        $compiledRoute->expects($this->any())->method("getMiddleware")->willReturn([]);
        $compiledRoute->expects($this->any())->method("getPathVars")->willReturn([]);
        $parsedRoute = $this->getMock(ParsedRoute::class, [], [], "", false);
        $parsedRoute->expects($this->any())->method("getMethods")->willReturn([$method]);
        /** @var IParser|\PHPUnit_Framework_MockObject_MockObject $routeParser */
        $routeParser = $this->getMock(IParser::class);
        $routeParser->expects($this->any())->method("parse")->willReturn($parsedRoute);
        /** @var ICompiler|\PHPUnit_Framework_MockObject_MockObject $routeCompiler */
        $routeCompiler = $this->getMock(ICompiler::class);
        $routeCompiler->expects($this->any())->method("compile")->willReturn($compiledRoute);

        if($shouldThrowException)
        {
            $router = new ExceptionalRouter(new Dispatcher($container), $routeCompiler, $routeParser);
        }
        else
        {
            $router = new Router(new Dispatcher($container), $routeCompiler, $routeParser);
        }

        $router->any("/", Controller::class . "@noParameters");
        $logger = new Logger("kernelTest");
        $logger->pushHandler(new MonologHandler());

        return new Kernel($container, $router, $logger);
    }
}