<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Testing\PhpUnit\Http;

use Opulence\Debug\Exceptions\Handlers\IExceptionHandler;
use Opulence\Environments\Environment;
use Opulence\Framework\Debug\Exceptions\Handlers\Http\IExceptionRenderer;
use Opulence\Framework\Http\Kernel;
use Opulence\Framework\Testing\PhpUnit\Http\Assertions\ResponseAssertions;
use Opulence\Framework\Testing\PhpUnit\Http\Assertions\ViewAssertions;
use Opulence\Framework\Testing\PhpUnit\IntegrationTestCase as BaseIntegrationTestCase;
use Opulence\Http\Requests\Request;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Router;

/**
 * Defines the HTTP integration test
 */
abstract class IntegrationTestCase extends BaseIntegrationTestCase
{
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
    public function delete($url = null)
    {
        return new RequestBuilder($this, RequestMethods::DELETE, $url);
    }

    /**
     * Creates a request builder for a GET request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function get($url = null)
    {
        return new RequestBuilder($this, RequestMethods::GET, $url);
    }

    /**
     * Creates a request builder for a HEAD request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function head($url = null)
    {
        return new RequestBuilder($this, RequestMethods::HEAD, $url);
    }

    /**
     * Creates a request builder for an OPTIONS request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function options($url = null)
    {
        return new RequestBuilder($this, RequestMethods::OPTIONS, $url);
    }

    /**
     * Creates a request builder for a PATCH request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function patch($url = null)
    {
        return new RequestBuilder($this, RequestMethods::PATCH, $url);
    }

    /**
     * Creates a request builder for a POST request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function post($url = null)
    {
        return new RequestBuilder($this, RequestMethods::POST, $url);
    }

    /**
     * Creates a request builder for a PUT request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function put($url = null)
    {
        return new RequestBuilder($this, RequestMethods::PUT, $url);
    }

    /**
     * Simulates a route for use in testing
     *
     * @param Request|null $request The request to use
     * @return $this For method chaining
     */
    public function route(Request $request = null)
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
        $this->container->bind(IExceptionHandler::class, $this->getExceptionHandler());
        $this->container->bind(IExceptionRenderer::class, $this->getExceptionRenderer());
        $this->router = $this->container->makeShared(Router::class);
        $this->kernel = $this->container->makeShared(Kernel::class);
        $this->kernel->addMiddleware($this->getGlobalMiddleware());
        $this->defaultRequest = new Request([], [], [], [], [], []);
        $this->assertResponse = new ResponseAssertions();
        $this->assertView = new ViewAssertions();
    }

    /**
     * Gets the kernel exception handler
     *
     * @return IExceptionHandler The exception handler used in the kernel
     */
    abstract protected function getExceptionHandler();

    /**
     * Gets the exception renderer
     *
     * @return IExceptionRenderer The exception renderer
     */
    abstract protected function getExceptionRenderer();

    /**
     * Gets the list of global middleware
     *
     * @return array The list of global middleware classes
     */
    abstract protected function getGlobalMiddleware();
}