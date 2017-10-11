<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Tests\Http;

use Opulence\Debug\Exceptions\Handlers\ExceptionHandler;
use Opulence\Framework\Debug\Exceptions\Handlers\Http\IExceptionRenderer;
use Opulence\Framework\Http\Kernel;
use Opulence\Http\Requests\Request;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Opulence\Ioc\Container;
use Opulence\Routing\Dispatchers\ContainerDependencyResolver;
use Opulence\Routing\Dispatchers\MiddlewarePipeline;
use Opulence\Routing\Dispatchers\RouteDispatcher;
use Opulence\Routing\Middleware\MiddlewareParameters;
use Opulence\Routing\Router;
use Opulence\Routing\Routes\CompiledRoute;
use Opulence\Routing\Routes\Compilers\ICompiler;
use Opulence\Routing\Routes\Compilers\Parsers\IParser;
use Opulence\Routing\Routes\ParsedRoute;
use Opulence\Routing\Tests\Middleware\Mocks\HeaderSetter;
use Opulence\Routing\Tests\Middleware\Mocks\ParameterizedMiddleware;
use Opulence\Routing\Tests\Mocks\Controller;
use Opulence\Routing\Tests\Mocks\ExceptionalRouter;
use Psr\Log\LoggerInterface;

/**
 * Tests the HTTP kernel
 */
class KernelTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests adding empty middleware
     */
    public function testAddingEmptyMiddleware()
    {
        $kernel = $this->getKernel(RequestMethods::GET, false);
        $kernel->addMiddleware([]);
        $this->assertEquals([], $kernel->getMiddleware());
    }

    /**
     * Tests adding middleware
     */
    public function testAddingMiddleware()
    {
        $kernel = $this->getKernel(RequestMethods::GET, false);
        // Test a single middleware
        $kernel->addMiddleware('foo');
        $this->assertEquals(['foo'], $kernel->getMiddleware());
        // Test multiple middleware
        $kernel->addMiddleware(['bar', 'baz']);
        $this->assertEquals(['foo', 'bar', 'baz'], $kernel->getMiddleware());
    }

    /**
     * Tests disabling all middleware
     */
    public function testDisablingAllMiddleware()
    {
        $kernel = $this->getKernel(RequestMethods::GET, false);
        $kernel->addMiddleware('foo');
        $kernel->disableAllMiddleware();
        $this->assertEquals([], $kernel->getMiddleware());
    }

    /**
     * Tests disabling certain middleware
     */
    public function testDisablingCertainMiddleware()
    {
        $kernel = $this->getKernel(RequestMethods::GET, false);
        $parameterizedMiddleware = new MiddlewareParameters('parameterized', []);
        $middlewareObject = new HeaderSetter();
        $kernel->addMiddleware('foo');
        $kernel->addMiddleware($parameterizedMiddleware);
        $kernel->addMiddleware($middlewareObject);
        $kernel->onlyDisableMiddleware(['foo']);
        $this->assertEquals([$parameterizedMiddleware, $middlewareObject], $kernel->getMiddleware());
        $kernel->onlyDisableMiddleware(['parameterized']);
        $this->assertEquals(['foo', $middlewareObject], $kernel->getMiddleware());
        $kernel->onlyDisableMiddleware([get_class($middlewareObject)]);
        $this->assertEquals(['foo', $parameterizedMiddleware], $kernel->getMiddleware());
    }

    /**
     * Tests enabling certain middleware
     */
    public function testEnablingCertainMiddleware()
    {
        $kernel = $this->getKernel(RequestMethods::GET, false);
        $kernel->addMiddleware('foo');
        $kernel->addMiddleware('bar');
        $kernel->onlyEnableMiddleware(['foo']);
        $this->assertEquals(['foo'], $kernel->getMiddleware());
    }

    /**
     * Tests getting middleware
     */
    public function testGettingMiddleware()
    {
        $kernel = $this->getKernel(RequestMethods::GET, false);
        $this->assertEquals([], $kernel->getMiddleware());
        $kernel->addMiddleware('foo');
        $this->assertEquals(['foo'], $kernel->getMiddleware());
    }

    /**
     * Tests handling an exceptional request
     */
    public function testHandlingExceptionalRequest()
    {
        $kernel = $this->getKernel(RequestMethods::GET, true);
        $request = Request::createFromGlobals();
        $response = $kernel->handle($request);
        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * Tests handling a request
     */
    public function testHandlingRequest()
    {
        $kernel = $this->getKernel(RequestMethods::GET, false);
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
        $kernel = $this->getKernel(RequestMethods::GET, false);
        $kernel->addMiddleware(HeaderSetter::class);
        $request = Request::createFromGlobals();
        $response = $kernel->handle($request);
        $this->assertEquals('bar', $response->getHeaders()->get('foo'));
    }

    /**
     * Tests handling a request with parameterized middleware
     */
    public function testHandlingWithParameterizedMiddleware()
    {
        $kernel = $this->getKernel(RequestMethods::GET, false);
        $kernel->addMiddleware(ParameterizedMiddleware::withParameters(['foo' => 'bar']));
        $request = Request::createFromGlobals();
        $response = $kernel->handle($request);
        $this->assertEquals('middleware', $response->getHeaders()->get('parameterized'));
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
        $dependencyResolver = new ContainerDependencyResolver($container);
        $compiledRoute = $this->getMockBuilder(CompiledRoute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $compiledRoute->expects($this->any())->method('isMatch')->willReturn(true);
        $compiledRoute->expects($this->any())->method('getControllerName')->willReturn(Controller::class);
        $compiledRoute->expects($this->any())->method('getControllerMethod')->willReturn('noParameters');
        $compiledRoute->expects($this->any())->method('getMiddleware')->willReturn([]);
        $compiledRoute->expects($this->any())->method('getPathVars')->willReturn([]);
        $parsedRoute = $this->getMockBuilder(ParsedRoute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parsedRoute->expects($this->any())->method('getMethods')->willReturn([$method]);
        /** @var IParser|\PHPUnit_Framework_MockObject_MockObject $routeParser */
        $routeParser = $this->createMock(IParser::class);
        $routeParser->expects($this->any())->method('parse')->willReturn($parsedRoute);
        /** @var ICompiler|\PHPUnit_Framework_MockObject_MockObject $routeCompiler */
        $routeCompiler = $this->createMock(ICompiler::class);
        $routeCompiler->expects($this->any())->method('compile')->willReturn($compiledRoute);

        if ($shouldThrowException) {
            $router = new ExceptionalRouter(
                new RouteDispatcher($dependencyResolver, new MiddlewarePipeline()),
                $routeCompiler,
                $routeParser
            );
        } else {
            $router = new Router(
                new RouteDispatcher($dependencyResolver, new MiddlewarePipeline()),
                $routeCompiler,
                $routeParser
            );
        }

        $router->any('/', Controller::class . '@noParameters');
        /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this->createMock(LoggerInterface::class);
        /** @var IExceptionRenderer|\PHPUnit_Framework_MockObject_MockObject $exceptionRenderer */
        $exceptionRenderer = $this->createMock(IExceptionRenderer::class);
        $exceptionRenderer->expects($this->any())
            ->method('getResponse')
            ->willReturn($this->createMock(Response::class));
        $exceptionHandler = new ExceptionHandler($logger, $exceptionRenderer);

        return new Kernel($container, $router, $exceptionHandler, $exceptionRenderer);
    }
}
