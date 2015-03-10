<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the controller used by the HTTP application test
 */
namespace RDev\Tests\HTTP\Routing\Mocks;
use RDev\HTTP\Responses;
use RDev\HTTP\Routing;
use RDev\Views;

class HTTPApplicationTestController extends Routing\Controller
{
    /**
     * Creates a redirect response
     *
     * @return Responses\RedirectResponse The response
     */
    public function redirect()
    {
        return new Responses\RedirectResponse("/redirectedPath");
    }

    /**
     * Sets a bad gateway status code in the response
     *
     * @return Responses\Response The response
     */
    public function setBadGateway()
    {
        return new Responses\Response("FooBar", Responses\ResponseHeaders::HTTP_BAD_GATEWAY);
    }

    /**
     * Sets a cookie in the response
     *
     * @return Responses\Response The response
     */
    public function setCookie()
    {
        $response = new Responses\Response("FooBar");
        $response->getHeaders()->setCookie(new Responses\Cookie("foo", "bar", new \DateTime()));

        return $response;
    }

    /**
     * Sets a header in the response
     *
     * @return Responses\Response The response
     */
    public function setHeader()
    {
        $response = new Responses\Response("FooBar");
        $response->getHeaders()->set("foo", "bar");

        return $response;
    }

    /**
     * Sets a tag in the template
     *
     * @return Responses\Response The response
     */
    public function setTag()
    {
        $this->template = new Views\Template("thecontent");
        $this->template->setTag("foo", "bar");

        return new Responses\Response("FooBar");
    }

    /**
     * Sets a variable in the template
     *
     * @return Responses\Response The response
     */
    public function setVar()
    {
        $this->template = new Views\Template("thecontent");
        $this->template->setVar("foo", "bar");

        return new Responses\Response("FooBar");
    }

    /**
     * Sets an internal server error in the response
     *
     * @return Responses\Response The response
     */
    public function setISE()
    {
        return new Responses\Response("FooBar", Responses\ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Sets an OK response
     *
     * @return Responses\Response The response
     */
    public function setOK()
    {
        return new Responses\Response("FooBar", Responses\ResponseHeaders::HTTP_OK);
    }

    /**
     * Sets an unauthorized response
     *
     * @return Responses\Response The response
     */
    public function setUnauthorized()
    {
        return new Responses\Response("FooBar", Responses\ResponseHeaders::HTTP_UNAUTHORIZED);
    }

    /**
     * Shows "FooBar" in the response content
     *
     * @return Responses\Response The response
     */
    public function showFooBar()
    {
        return new Responses\Response("FooBar");
    }
}