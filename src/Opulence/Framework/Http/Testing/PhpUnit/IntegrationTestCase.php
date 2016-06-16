<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Http\Testing\PhpUnit;

use Opulence\Debug\Exceptions\Handlers\IExceptionHandler;
use Opulence\Environments\Environment;
use Opulence\Framework\Debug\Exceptions\Handlers\Http\IExceptionRenderer;
use Opulence\Framework\Http\Kernel;
use Opulence\Framework\Http\Testing\PhpUnit\Assertions\ResponseAssertions;
use Opulence\Framework\Http\Testing\PhpUnit\Assertions\ViewAssertions;
use Opulence\Http\Requests\Request;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Router;
use PHPUnit\Framework\TestCase;

/**
 * Defines the HTTP integration test
 */
abstract class IntegrationTestCase extends TestCase
{
    /** @var Application The application */
    protected $application = null;
    /** @var Environment The current environment */
    protected $environment = null;
    /** @var IContainer The IoC container */
    protected $container = null;
    /** @var Router The router */
    protected $router = null;
    /** @var Request The default request */
    protected $defaultRequest = null;
    /** @var Kernel The HTTP kernel */
    protected $kernel = null;
    /** @var Response The response from the last route */
    protected $response = null;
    /** @var ResponseAssertions The response assertions */
    protected $assertResponse = null;
    /** @var ViewAssertions The view assertions */
    protected $assertView = null;

    /**
     * Creates a request builder for a DELETE request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function delete(string $url = null) : RequestBuilder
    {
        return new RequestBuilder($this, RequestMethods::DELETE, $url);
    }

    /**
     * Creates a request builder for a GET request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function get(string $url = null) : RequestBuilder
    {
        return new RequestBuilder($this, RequestMethods::GET, $url);
    }

    /**
     * Creates a request builder for a HEAD request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function head(string $url = null) : RequestBuilder
    {
        return new RequestBuilder($this, RequestMethods::HEAD, $url);
    }

    /**
     * Creates a request builder for an OPTIONS request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function options(string $url = null) : RequestBuilder
    {
        return new RequestBuilder($this, RequestMethods::OPTIONS, $url);
    }

    /**
     * Creates a request builder for a PATCH request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function patch(string $url = null) : RequestBuilder
    {
        return new RequestBuilder($this, RequestMethods::PATCH, $url);
    }

    /**
     * Creates a request builder for a POST request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function post(string $url = null) : RequestBuilder
    {
        return new RequestBuilder($this, RequestMethods::POST, $url);
    }

    /**
     * Creates a request builder for a PUT request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function put(string $url = null) : RequestBuilder
    {
        return new RequestBuilder($this, RequestMethods::PUT, $url);
    }

    /**
     * Simulates a route for use in testing
     *
     * @param Request|null $request The request to use
     * @return self For method chaining
     */
    public function route(Request $request = null) : self
    {
        if ($request === null) {
            $request = $this->defaultRequest;
        }

        $this->response = $this->kernel->handle($request);
        $this->assertResponse->setResponse($this->response);
        $this->assertView->setController($this->router->getMatchedController());

        return $this;
    }

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->environment->setName(Environment::TESTING);
        $this->application->start();
        $this->container->bindInstance(IExceptionHandler::class, $this->getExceptionHandler());
        $this->container->bindInstance(IExceptionRenderer::class, $this->getExceptionRenderer());
        $this->router = $this->container->resolve(Router::class);
        $this->kernel = $this->container->resolve(Kernel::class);
        $this->kernel->addMiddleware($this->getGlobalMiddleware());
        $this->defaultRequest = new Request([], [], [], [], [], []);
        $this->assertResponse = new ResponseAssertions();
        $this->assertView = new ViewAssertions();
    }

    /**
     * Tears down the tests
     */
    public function tearDown()
    {
        $this->application->shutDown();
    }

    /**
     * Gets the kernel exception handler
     *
     * @return IExceptionHandler The exception handler used in the kernel
     */
    abstract protected function getExceptionHandler() : IExceptionHandler;

    /**
     * Gets the exception renderer
     *
     * @return IExceptionRenderer The exception renderer
     */
    abstract protected function getExceptionRenderer() : IExceptionRenderer;

    /**
     * Gets the list of global middleware
     *
     * @return array The list of global middleware classes
     */
    abstract protected function getGlobalMiddleware() : array;
}