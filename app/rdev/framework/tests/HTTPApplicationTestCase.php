<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the HTTP application test case
 */
namespace RDev\Framework\Tests;
use RDev\Applications;
use RDev\HTTP\Kernels;
use RDev\HTTP\Requests;
use RDev\HTTP\Responses;
use RDev\HTTP\Routing;

abstract class HTTPApplicationTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var Applications\Application The application */
    protected $application = null;
    /** @var Routing\Router The router */
    protected $router = null;
    /** @var Requests\Request The default request */
    protected $defaultRequest = null;
    /** @var Kernels\Kernel The HTTP kernel */
    protected $kernel = null;
    /** @var Responses\Response The response from the last route */
    protected $response = null;

    /**
     * Asserts that the response redirects to a URL
     *
     * @param string $url The expected URL
     */
    public function assertRedirectsTo($url)
    {
        $this->checkResponseIsSet();
        $this->assertTrue(
            $this->response instanceof Responses\RedirectResponse &&
            $this->response->getTargetURL() == $url
        );
	}

    /**
     * Asserts that the response contents' matches the input
     *
     * @param mixed $expected The expected value
     */
    public function assertResponseContentEquals($expected)
    {
        $this->checkResponseIsSet();
        $this->assertEquals($expected, $this->response->getContent());
    }

    /**
     * Asserts that the response's cookie's value equals the input
     *
     * @param string $name The name of the cookie to search for
     * @param mixed $expected The expected value
     */
    public function assertResponseCookieValueEquals($name, $expected)
    {
        $this->checkResponseIsSet();
        $cookies = $this->response->getHeaders()->getCookies();
        $cookieValue = null;

        foreach($cookies as $cookie)
        {
            if($cookie->getName() == $name)
            {
                $cookieValue = $cookie->getValue();

                break;
            }
        }

        $this->assertEquals($expected, $cookieValue);
    }

    /**
     * Asserts that the response has a cookie
     *
     * @param string $name The name of the cookie to search for
     */
    public function assertResponseHasCookie($name)
    {
        $this->checkResponseIsSet();
        $cookies = $this->response->getHeaders()->getCookies();
        $wasFound = false;

        foreach($cookies as $cookie)
        {
            if($cookie->getName() == $name)
            {
                $wasFound = true;

                break;
            }
        }

        $this->assertTrue($wasFound);
    }

    /**
     * Asserts that the response has a header
     *
     * @param string $name The name of the cookie to search for
     */
    public function assertResponseHasHeader($name)
    {
        $this->checkResponseIsSet();
        $this->assertTrue($this->response->getHeaders()->has($name));
    }

    /**
     * Asserts that the response's header's value equals the input
     *
     * @param string $name The name of the header to search for
     * @param mixed $expected The expected value
     */
    public function assertResponseHeaderEquals($name, $expected)
    {
        $this->checkResponseIsSet();
        $this->assertEquals($expected, $this->response->getHeaders()->get($name));
    }

    /**
     * Asserts that the response is an internal server error
     */
    public function assertResponseInternalServerError()
    {
        $this->checkResponseIsSet();
        $this->assertEquals(Responses\ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR, $this->response->getStatusCode());
    }

    /**
     * Asserts that the response is not found
     */
    public function assertResponseNotFound()
    {
        $this->checkResponseIsSet();
        $this->assertEquals(Responses\ResponseHeaders::HTTP_NOT_FOUND, $this->response->getStatusCode());
    }

    /**
     * Asserts that the response is OK
     */
    public function assertResponseOK()
    {
        $this->checkResponseIsSet();
        $this->assertEquals(Responses\ResponseHeaders::HTTP_OK, $this->response->getStatusCode());
    }

    /**
     * Asserts that the response is OK
     *
     * @param int $statusCode The expected status code
     */
    public function assertResponseStatusCode($statusCode)
    {
        $this->checkResponseIsSet();
        $this->assertEquals($statusCode, $this->response->getStatusCode());
    }

    /**
     * Asserts that the response is unauthorized
     */
    public function assertResponseUnauthorized()
    {
        $this->checkResponseIsSet();
        $this->assertEquals(Responses\ResponseHeaders::HTTP_UNAUTHORIZED, $this->response->getStatusCode());
    }

    /**
     * Asserts that the template has a tag
     *
     * @param string $name The name of the tag to search for
     */
    public function assertTemplateHasTag($name)
    {
        $this->checkResponseIsSet();
        $this->assertNotNull($this->router->getMatchedController()->getTemplate()->getTag($name));
    }

    /**
     * Asserts that the template has a variable
     *
     * @param string $name The name of the variable to search for
     */
    public function assertTemplateHasVar($name)
    {
        $this->checkResponseIsSet();
        $this->assertNotNull($this->router->getMatchedController()->getTemplate()->getVar($name));
    }

    /**
     * Asserts that the template has a tag with a certain value
     *
     * @param string $name The name of the tag to search for
     * @param mixed $expected The expected value
     */
    public function assertTemplateTagEquals($name, $expected)
    {
        $this->checkResponseIsSet();
        $this->assertEquals($expected, $this->router->getMatchedController()->getTemplate()->getTag($name));
    }

    /**
     * Asserts that the template has a variable with a certain value
     *
     * @param string $name The name of the tag to search for
     * @param mixed $expected The expected value
     */
    public function assertTemplateVarEquals($name, $expected)
    {
        $this->checkResponseIsSet();
        $this->assertEquals($expected, $this->router->getMatchedController()->getTemplate()->getVar($name));
    }

    /**
     * @return Applications\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return Kernels\Kernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * @return Routing\Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Simulates a route for use in testing
     *
     * @param string $method The HTTP method to use
     * @param string $url The URL to route
     * @param Requests\Request|null $request The request to use
     * @return Responses\Response The response
     */
    public function route($method, $url, Requests\Request $request = null)
    {
        if($request === null)
        {
            $request = $this->defaultRequest;
            $parsedURL = parse_url($url);
            $request->setPath($parsedURL["path"]);
            $request->setMethod(strtoupper($method));
            $request->getHeaders()->set("HOST", isset($parsedURL["host"]) ? $parsedURL["host"] : "");
        }

        $this->response = $this->router->route($request);

        return $this->response;
    }

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->setApplication();
        $this->application->start();
        $this->router = $this->application->getIoCContainer()->makeShared("RDev\\HTTP\\Routing\\Router");
        $this->kernel = $this->application->getIoCContainer()->makeShared("RDev\\HTTP\\Kernels\\Kernel");
        $this->defaultRequest = new Requests\Request([], [], [], [], [], []);
    }

    /**
     * Tears down the tests
     */
    public function tearDown()
    {
        $this->application->shutdown();
    }

    /**
     * Sets the instance of the application to use in tests
     */
    abstract protected function setApplication();

    /**
     * Checks if the response was set
     * Useful for making sure the response was set before making any assertions on it
     */
    private function checkResponseIsSet()
    {
        if($this->response === null)
        {
            $this->fail("Must call route() before assertions");
        }
    }
}