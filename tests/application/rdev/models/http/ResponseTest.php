<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the HTTP response
 */
namespace RDev\Models\HTTP;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /** @var Response The response to use in tests */
    private $response = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->response = new Response();
    }

    /**
     * Tests getting the content
     */
    public function testGettingContent()
    {
        $response = new Response("foo");
        $this->assertEquals("foo", $response->getContent());
    }

    /**
     * Tests getting the default HTTP version
     */
    public function testGettingDefaultHTTPVersion()
    {
        $this->assertEquals("1.1", $this->response->getHTTPVersion());
    }

    /**
     * Tests getting the default status code
     */
    public function testGettingDefaultStatusCode()
    {
        $this->assertEquals(ResponseHeaders::HTTP_OK, $this->response->getStatusCode());
    }

    /**
     * Tests sending the content
     *
     * @runInSeparateProcess
     */
    public function testSendingContent()
    {
        $this->response->setContent("foo");
        ob_start();
        $this->response->sendContent();
        $this->assertEquals("foo", ob_get_clean());
    }

    /**
     * Tests setting the content
     */
    public function testSettingContent()
    {
        $this->response->setContent("foo");
        $this->assertEquals("foo", $this->response->getContent());
    }

    /**
     * Tests setting an expiration
     */
    public function testSettingExpiration()
    {
        $expiration = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->response->setExpiration($expiration);
        $this->assertEquals([$expiration->format("r")], $this->response->getHeaders()->get("Expires"));
    }

    /**
     * Tests setting the HTTP version
     */
    public function testSettingHTTPVersion()
    {
        $this->response->setHTTPVersion("2.0");
        $this->assertEquals("2.0", $this->response->getHTTPVersion());
    }

    /**
     * Tests setting the status code
     */
    public function testSettingStatusCode()
    {
        $this->response->setStatusCode(ResponseHeaders::HTTP_ACCEPTED);
        $this->assertEquals(ResponseHeaders::HTTP_ACCEPTED, $this->response->getStatusCode());
    }

    /**
     * Tests setting the status code with text
     */
    public function testSettingStatusCodeWithText()
    {
        $this->response->setStatusCode(ResponseHeaders::HTTP_ACCEPTED, ResponseHeaders::$statusTexts[ResponseHeaders::HTTP_ACCEPTED]);
        $this->assertEquals(ResponseHeaders::HTTP_ACCEPTED, $this->response->getStatusCode());
    }
}