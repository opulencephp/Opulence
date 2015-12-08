<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Testing\PhpUnit\Http;

use LogicException;
use Opulence\Debug\Exceptions\Handlers\IExceptionHandler;
use Opulence\Environments\Environment;
use Opulence\Framework\Debug\Exceptions\Handlers\Http\IExceptionRenderer;
use Opulence\Framework\Http\Kernel;
use Opulence\Framework\Testing\PhpUnit\ApplicationTestCase as BaseApplicationTestCase;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\RedirectResponse;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Opulence\Routing\Router;
use Opulence\Routing\Controller;

/**
 * Defines the HTTP application test case
 */
abstract class ApplicationTestCase extends BaseApplicationTestCase
{
    /** @var Router The router */
    protected $router = null;
    /** @var Request The default request */
    protected $defaultRequest = null;
    /** @var Kernel The HTTP kernel */
    protected $kernel = null;
    /** @var Response The response from the last route */
    protected $response = null;
    /** @var RequestBuilder|null The last request builder run */
    private $lastRequestBuilder = null;

    /**
     * Asserts that the response redirects to a URL
     *
     * @param string $url The expected URL
     * @return $this For method chaining
     */
    public function assertRedirectsTo($url)
    {
        $this->checkResponseIsSet();
        $this->assertTrue(
            $this->response instanceof RedirectResponse && $this->response->getTargetUrl() == $url,
            "Failed asserting that the response redirects to \"$url\""
        );

        return $this;
    }

    /**
     * Asserts that the response's contents match the input
     *
     * @param mixed $expected The expected value
     * @return $this For method chaining
     */
    public function assertResponseContentEquals($expected)
    {
        $this->checkResponseIsSet();
        $this->assertEquals($expected, $this->response->getContent());

        return $this;
    }

    /**
     * Asserts that the response's cookie's value equals the input
     *
     * @param string $name The name of the cookie to search for
     * @param mixed $expected The expected value
     * @return $this For method chaining
     */
    public function assertResponseCookieValueEquals($name, $expected)
    {
        $this->checkResponseIsSet();
        $cookies = $this->response->getHeaders()->getCookies();
        $cookieValue = null;

        foreach ($cookies as $cookie) {
            if ($cookie->getName() == $name) {
                $cookieValue = $cookie->getValue();

                break;
            }
        }

        $this->assertEquals($expected, $cookieValue);

        return $this;
    }

    /**
     * Asserts that the response has a cookie
     *
     * @param string $name The name of the cookie to search for
     * @return $this For method chaining
     */
    public function assertResponseHasCookie($name)
    {
        $this->checkResponseIsSet();
        $cookies = $this->response->getHeaders()->getCookies();
        $wasFound = false;

        foreach ($cookies as $cookie) {
            if ($cookie->getName() == $name) {
                $wasFound = true;

                break;
            }
        }

        $this->assertTrue($wasFound, "Failed asserting that the response has cookie \"$name\"");

        return $this;
    }

    /**
     * Asserts that the response has a header
     *
     * @param string $name The name of the header to search for
     * @return $this For method chaining
     */
    public function assertResponseHasHeader($name)
    {
        $this->checkResponseIsSet();
        $this->assertTrue(
            $this->response->getHeaders()->has($name),
            "Failed asserting that the response has header \"$name\""
        );

        return $this;
    }

    /**
     * Asserts that the response's header's value equals the input
     *
     * @param string $name The name of the header to search for
     * @param mixed $expected The expected value
     * @return $this For method chaining
     */
    public function assertResponseHeaderEquals($name, $expected)
    {
        $this->checkResponseIsSet();
        $this->assertEquals($expected, $this->response->getHeaders()->get($name));

        return $this;
    }

    /**
     * Asserts that the response is an internal server error
     *
     * @return $this For method chaining
     */
    public function assertResponseIsInternalServerError()
    {
        $this->checkResponseIsSet();
        $this->assertEquals(ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR, $this->response->getStatusCode());

        return $this;
    }

    /**
     * Asserts that the response is not found
     *
     * @return $this For method chaining
     */
    public function assertResponseIsNotFound()
    {
        $this->checkResponseIsSet();
        $this->assertEquals(ResponseHeaders::HTTP_NOT_FOUND, $this->response->getStatusCode());

        return $this;
    }

    /**
     * Asserts that the response is OK
     *
     * @return $this For method chaining
     */
    public function assertResponseIsOK()
    {
        $this->checkResponseIsSet();
        $this->assertEquals(ResponseHeaders::HTTP_OK, $this->response->getStatusCode());

        return $this;
    }

    /**
     * Asserts that the response is unauthorized
     *
     * @return $this For method chaining
     */
    public function assertResponseIsUnauthorized()
    {
        $this->checkResponseIsSet();
        $this->assertEquals(ResponseHeaders::HTTP_UNAUTHORIZED, $this->response->getStatusCode());

        return $this;
    }

    /**
     * Asserts that the response's JSON match the input
     *
     * @param array $expected The expected value
     * @return $this For method chaining
     */
    public function assertResponseJsonEquals(array $expected)
    {
        $this->checkResponseIsSet();
        $this->assertJson($this->response->getContent());
        $this->assertEquals($expected, json_decode($this->response->getContent(), true));

        return $this;
    }

    /**
     * Asserts that the response status code equals a particular value
     *
     * @param int $statusCode The expected status code
     * @return $this For method chaining
     */
    public function assertResponseStatusCodeEquals($statusCode)
    {
        $this->checkResponseIsSet();
        $this->assertEquals($statusCode, $this->response->getStatusCode());

        return $this;
    }

    /**
     * Asserts that the view has a variable
     *
     * @param string $name The name of the variable to search for
     * @return $this For method chaining
     * @throws LogicException Thrown if the controller does not extend the base controller
     */
    public function assertViewHasVar($name)
    {
        $this->checkResponseIsSet();

        if (!$this->router->getMatchedController() instanceof Controller) {
            throw new LogicException("Controller does not extend " . Controller::class);
        }

        $this->assertNotNull($this->router->getMatchedController()->getView()->getVar($name));

        return $this;
    }

    /**
     * Asserts that the view has a variable with a certain value
     *
     * @param string $name The name of the tag to search for
     * @param mixed $expected The expected value
     * @return $this For method chaining
     * @throws LogicException Thrown if the controller does not extend the base controller
     */
    public function assertViewVarEquals($name, $expected)
    {
        $this->checkResponseIsSet();

        if (!$this->router->getMatchedController() instanceof Controller) {
            throw new LogicException("Controller does not extend " . Controller::class);
        }

        $this->assertEquals($expected, $this->router->getMatchedController()->getView()->getVar($name));

        return $this;
    }

    /**
     * Creates a request builder for a DELETE request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function delete($url = null)
    {
        return $this->setLastRequestBuilder(new RequestBuilder($this, Request::METHOD_DELETE, $url));
    }

    /**
     * Creates a request builder for a GET request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function get($url = null)
    {
        return $this->setLastRequestBuilder(new RequestBuilder($this, Request::METHOD_GET, $url));
    }

    /**
     * @return Kernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Creates a request builder for a HEAD request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function head($url = null)
    {
        return $this->setLastRequestBuilder(new RequestBuilder($this, Request::METHOD_HEAD, $url));
    }

    /**
     * Creates a request builder for an OPTIONS request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function options($url = null)
    {
        return $this->setLastRequestBuilder(new RequestBuilder($this, Request::METHOD_OPTIONS, $url));
    }

    /**
     * Creates a request builder for a PATCH request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function patch($url = null)
    {
        return $this->setLastRequestBuilder(new RequestBuilder($this, Request::METHOD_PATCH, $url));
    }

    /**
     * Creates a request builder for a POST request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function post($url = null)
    {
        return $this->setLastRequestBuilder(new RequestBuilder($this, Request::METHOD_POST, $url));
    }

    /**
     * Creates a request builder for a PUT request
     *
     * @param string|null $url The URL to request
     * @return RequestBuilder The request builder
     */
    public function put($url = null)
    {
        return $this->setLastRequestBuilder(new RequestBuilder($this, Request::METHOD_PUT, $url));
    }

    /**
     * Simulates a route for use in testing
     *
     * @param Request|null $request The request to use
     * @return Response The response
     */
    public function route(Request $request = null)
    {
        if ($request === null) {
            $request = $this->defaultRequest;
        }

        $this->response = $this->kernel->handle($request);

        return $this->response;
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

    /**
     * Checks if the response was set
     * Useful for making sure the response was set before making any assertions on it
     */
    private function checkResponseIsSet()
    {
        if ($this->response === null) {
            if ($this->lastRequestBuilder === null) {
                $this->fail("Must call route() before assertions");
            } else {
                $this->lastRequestBuilder->go();
                // Unset it for next time
                $this->lastRequestBuilder = null;
            }
        }
    }

    /**
     * Sets the last request builder
     *
     * @param RequestBuilder $requestBuilder The last request builder
     * @return RequestBuilder The last request builder
     */
    private function setLastRequestBuilder(RequestBuilder $requestBuilder)
    {
        $this->lastRequestBuilder = $requestBuilder;

        return $requestBuilder;
    }
}