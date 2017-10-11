<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Tests\Dispatchers;

use InvalidArgumentException;
use Opulence\Http\HttpException;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Opulence\Routing\Dispatchers\RouteDispatcher;
use Opulence\Routing\Middleware\MiddlewareParameters;
use Opulence\Routing\RouteException;
use Opulence\Routing\Routes\CompiledRoute;
use Opulence\Routing\Routes\ParsedRoute;
use Opulence\Routing\Routes\Route;
use Opulence\Routing\Tests\Middleware\Mocks\DoesNotReturnSomethingMiddleware;
use Opulence\Routing\Tests\Middleware\Mocks\ParameterizedMiddleware;
use Opulence\Routing\Tests\Middleware\Mocks\ReturnsSomethingMiddleware;
use Opulence\Routing\Tests\Mocks\Controller as MockController;
use Opulence\Routing\Tests\Mocks\NonOpulenceController;
use Opulence\Views\Compilers\ICompiler;
use Opulence\Views\Factories\IViewFactory;

/**
 * Tests the route dispatcher class
 */
class RouteDispatcherTest extends \PHPUnit\Framework\TestCase
{
    /** @var RouteDispatcher The dispatcher to use in tests */
    private $dispatcher = null;
    /** @var Request The request to use in tests */
    private $request = null;
    /** @var IDependencyResolver|\PHPUnit_Framework_MockObject_MockObject The dependency resolver to use in tests */
    private $dependencyResolver = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->dependencyResolver = $this->createMock(IDependencyResolver::class);
        $this->dependencyResolver->expects($this->any())
            ->method('resolve')
            ->willReturnCallback(function () {
                $interface = func_get_arg(0);

                switch ($interface) {
                    case MockController::class:
                        return new MockController();
                    case NonOpulenceController::class:
                        return new NonOpulenceController(Request::createFromGlobals());
                    case IViewFactory::class:
                        return $this->createMock(IViewFactory::class);
                    case ICompiler::class:
                        return $this->createMock(ICompiler::class);
                    case __CLASS__:
                        return $this;
                    case DoesNotReturnSomethingMiddleware::class:
                        return new DoesNotReturnSomethingMiddleware();
                    case ReturnsSomethingMiddleware::class:
                        return new ReturnsSomethingMiddleware();
                    case ParameterizedMiddleware::class:
                        return new ParameterizedMiddleware();
                    case Request::class:
                        return Request::createFromGlobals();
                    default:
                        throw new InvalidArgumentException("Interface $interface is not setup in mock");
                }
            });
        $this->dispatcher = new RouteDispatcher($this->dependencyResolver, new MiddlewarePipeline());
        $this->request = new Request([], [], [], [], [], []);
    }

    /**
     * Tests calling a closure as a controller
     */
    public function testCallingClosure()
    {
        $route = $this->getCompiledRoute(
            new Route(['GET'], '/foo', function () {
                return new Response('Closure');
            })
        );
        $controller = null;
        $response = $this->dispatcher->dispatch($route, $this->request, $controller);
        $this->assertInstanceOf('Closure', $controller);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('Closure', $response->getContent());
    }

    /**
     * Tests calling a closure with a dependency
     */
    public function testCallingClosureWithDependencies()
    {
        $this->dependencyResolver->expects($this->once())
            ->method('resolve')
            ->with(Request::class)
            ->willReturn($this->request);
        $route = $this->getCompiledRoute(
            new Route(['GET'], '/foo/{primitive}', function (Request $request, $primitive) {
                return get_class($request) . ':' . $primitive;
            })
        );
        $route->setPathVars(['primitive' => 123]);
        $controller = null;
        $response = $this->dispatcher->dispatch($route, $this->request, $controller);
        $this->assertInstanceOf('Closure', $controller);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Request::class . ':123', $response->getContent());
    }

    /**
     * Tests calling a method that throws an HTTP exception
     */
    public function testCallingMethodThatThrowsHttpException()
    {
        $this->expectException(HttpException::class);
        $route = $this->getCompiledRoute(
            new Route(['GET'], '/foo', MockController::class . '@throwsHttpException')
        );
        $this->dispatcher->dispatch($route, $this->request);
    }

    /**
     * Tests calling a non-existent controller
     */
    public function testCallingNonExistentController()
    {
        $this->expectException(RouteException::class);
        $route = $this->getCompiledRoute(
            new Route(['GET'], '/foo', 'Opulence\\Controller\\That\\Does\\Not\\Exist@foo')
        );
        $this->dispatcher->dispatch($route, $this->request);
    }

    /**
     * Tests calling a method that does not exists in a controller
     */
    public function testCallingNonExistentMethod()
    {
        $this->expectException(RouteException::class);
        $route = $this->getCompiledRoute(
            new Route(['GET'], '/foo', MockController::class . '@doesNotExist')
        );
        $this->dispatcher->dispatch($route, $this->request);
    }

    /**
     * Tests calling a non-Opulence controller
     */
    public function testCallingNonOpulenceController()
    {
        $route = $this->getCompiledRoute(
            new Route(['GET'], '/foo/123', NonOpulenceController::class . '@index')
        );
        $route->setPathVars(['id' => '123']);
        $controller = null;
        $response = $this->dispatcher->dispatch($route, $this->request, $controller);
        $this->assertInstanceOf(NonOpulenceController::class, $controller);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('Id: 123', $response->getContent());
    }

    /**
     * Tests calling a private method in a controller
     */
    public function testCallingPrivateMethod()
    {
        $this->expectException(RouteException::class);
        $route = $this->getCompiledRoute(
            new Route(['GET'], '/foo', MockController::class . '@privateMethod')
        );
        $this->dispatcher->dispatch($route, $this->request);
    }

    /**
     * Tests calling a protected method in a controller
     */
    public function testCallingProtectedMethod()
    {
        $route = $this->getCompiledRoute(
            new Route(['GET'], '/foo', MockController::class . '@protectedMethod')
        );
        $this->assertEquals('protectedMethod', $this->dispatcher->dispatch($route, $this->request)->getContent());
    }

    /**
     * Tests chaining middleware that do and do not return something
     */
    public function testChainingMiddlewareThatDoAndDoNotReturnSomething()
    {
        $controller = MockController::class . '@noParameters';
        $options = [
            'middleware' => [
                ReturnsSomethingMiddleware::class,
                DoesNotReturnSomethingMiddleware::class
            ]
        ];
        $route = $this->getCompiledRoute(new Route(['GET'], '/foo', $controller, $options));
        $this->assertEquals(
            'noParameters:something',
            $this->dispatcher->dispatch($route, $this->request)->getContent()
        );
    }

    /**
     * Tests that text from a closure response is wrapped into response object
     */
    public function testClosureResponseTextIsWrappedInObject()
    {
        $route = $this->getCompiledRoute(
            new Route(['GET'], '/foo', function () {
                return 'Closure';
            })
        );
        $response = $this->dispatcher->dispatch($route, $this->request, $controller);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('Closure', $response->getContent());
        $this->assertEquals(ResponseHeaders::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Tests specifying an invalid middleware
     */
    public function testInvalidMiddleware()
    {
        $this->expectException(RouteException::class);
        $controller = MockController::class . '@returnsNothing';
        $options = [
            'middleware' => get_class($this)
        ];
        $route = $this->getCompiledRoute(new Route(['GET'], '/foo', $controller, $options));
        $this->dispatcher->dispatch($route, $this->request);
    }

    /**
     * Tests using a middleware that returns something with a controller that also returns something
     */
    public function testMiddlewareThatAddsToControllerResponse()
    {
        $controller = MockController::class . '@noParameters';
        $options = [
            'middleware' => ReturnsSomethingMiddleware::class
        ];
        $route = $this->getCompiledRoute(new Route(['GET'], '/foo', $controller, $options));
        $this->assertEquals(
            'noParameters:something',
            $this->dispatcher->dispatch($route, $this->request)->getContent()
        );
    }

    /**
     * Tests not passing required path variable to closure
     */
    public function testNotPassingRequiredPathVariableToClosure()
    {
        $this->expectException(RouteException::class);
        $route = $this->getCompiledRoute(
            new Route(['GET'], '/foo', function ($id) {
                return new Response("Closure: Id: $id");
            })
        );
        $this->dispatcher->dispatch($route, $this->request, $controller);
    }

    /**
     * Tests passing path variable to closure
     */
    public function testPassingPathVariableToClosure()
    {
        $route = $this->getCompiledRoute(
            new Route(['GET'], '/foo/{id}', function ($id) {
                return new Response("Closure: Id: $id");
            })
        );
        $route->setPathVars(['id' => '123']);
        $controller = null;
        $response = $this->dispatcher->dispatch($route, $this->request, $controller);
        $this->assertInstanceOf('Closure', $controller);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('Closure: Id: 123', $response->getContent());
    }

    /**
     * Tests that text returned by a controller is wrapped in a response object
     */
    public function testTextReturnedByControllerIsWrappedInResponseObject()
    {
        $route = $this->getCompiledRoute(
            new Route(['GET'], '/foo', MockController::class . '@returnsText')
        );
        $response = $this->dispatcher->dispatch($route, $this->request);
        $this->assertEquals('returnsText', $response->getContent());
        $this->assertEquals(ResponseHeaders::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Tests that controller is set
     */
    public function testThatControllerIsSet()
    {
        $controller = null;
        $expectedControllerClass = MockController::class;
        $route = $this->getCompiledRoute(new Route(['GET'], '/foo', "$expectedControllerClass@returnsNothing"));
        $this->assertEquals(new Response(), $this->dispatcher->dispatch($route, $this->request, $controller));
        $this->assertInstanceOf($expectedControllerClass, $controller);
    }

    /**
     * Tests using default value for a path variable in a closure
     */
    public function testUsingDefaultValueForPathVariableInClosure()
    {
        $route = $this->getCompiledRoute(
            new Route(['GET'], '/foo/{id}', function ($id = '123') {
                return new Response("Closure: Id: $id");
            })
        );
        $controller = null;
        $response = $this->dispatcher->dispatch($route, $this->request, $controller);
        $this->assertInstanceOf('Closure', $controller);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('Closure: Id: 123', $response->getContent());
    }

    /**
     * Tests using a middleware that does not return anything
     */
    public function testUsingMiddlewareThatDoesNotReturnAnything()
    {
        $controller = MockController::class . '@returnsNothing';
        $options = [
            'middleware' => DoesNotReturnSomethingMiddleware::class
        ];
        $route = $this->getCompiledRoute(new Route(['GET'], '/foo', $controller, $options));
        $this->assertEquals(new Response(), $this->dispatcher->dispatch($route, $this->request));
    }

    /**
     * Tests using parameterized middleware
     */
    public function testUsingParameterizedMiddleware()
    {
        $controller = MockController::class . '@returnsNothing';
        $options = [
            'middleware' => new MiddlewareParameters(ParameterizedMiddleware::class, ['foo' => 'bar'])
        ];
        $route = $this->getCompiledRoute(new Route(['GET'], '/foo', $controller, $options));
        $response = $this->dispatcher->dispatch($route, $this->request);
        $this->assertEquals('bar', $response->getHeaders()->get('parameters'));
    }

    /**
     * Gets the compiled route
     *
     * @param Route $route The route to compile
     * @return CompiledRoute The compiled route
     */
    private function getCompiledRoute(Route $route)
    {
        $parsedRoute = new ParsedRoute($route);

        return new CompiledRoute($parsedRoute, false);
    }
}
