<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the controller used by the HTTP application test
 */
namespace RDev\Tests\HTTP\Routing\Mocks;
use DateTime;
use RDev\HTTP\Responses\Cookie;
use RDev\HTTP\Responses\RedirectResponse;
use RDev\HTTP\Responses\Response;
use RDev\HTTP\Responses\ResponseHeaders;
use RDev\HTTP\Routing\Controller;
use RDev\Views\Template;

class HTTPApplicationTestController extends Controller
{
    /**
     * Creates a redirect response
     *
     * @return RedirectResponse The response
     */
    public function redirect()
    {
        return new RedirectResponse("/redirectedPath");
    }

    /**
     * Sets a bad gateway status code in the response
     *
     * @return Response The response
     */
    public function setBadGateway()
    {
        return new Response("FooBar", ResponseHeaders::HTTP_BAD_GATEWAY);
    }

    /**
     * Sets a cookie in the response
     *
     * @return Response The response
     */
    public function setCookie()
    {
        $response = new Response("FooBar");
        $response->getHeaders()->setCookie(new Cookie("foo", "bar", new DateTime()));

        return $response;
    }

    /**
     * Sets a header in the response
     *
     * @return Response The response
     */
    public function setHeader()
    {
        $response = new Response("FooBar");
        $response->getHeaders()->set("foo", "bar");

        return $response;
    }

    /**
     * Sets an internal server error in the response
     *
     * @return Response The response
     */
    public function setISE()
    {
        return new Response("FooBar", ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Sets an OK response
     *
     * @return Response The response
     */
    public function setOK()
    {
        return new Response("FooBar", ResponseHeaders::HTTP_OK);
    }

    /**
     * Sets a tag in the template
     *
     * @return Response The response
     */
    public function setTag()
    {
        $this->template = new Template("thecontent");
        $this->template->setTag("foo", "bar");

        return new Response("FooBar");
    }

    /**
     * Sets an unauthorized response
     *
     * @return Response The response
     */
    public function setUnauthorized()
    {
        return new Response("FooBar", ResponseHeaders::HTTP_UNAUTHORIZED);
    }

    /**
     * Sets a variable in the template
     *
     * @return Response The response
     */
    public function setVar()
    {
        $this->template = new Template("thecontent");
        $this->template->setVar("foo", "bar");

        return new Response("FooBar");
    }

    /**
     * Shows "FooBar" in the response content
     *
     * @return Response The response
     */
    public function showFooBar()
    {
        return new Response("FooBar");
    }
}