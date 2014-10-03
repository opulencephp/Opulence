<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the HTTP connection class
 */
namespace RDev\Models\Web;

class HTTPConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the HTTP request
     */
    public function testGettingRequest()
    {
        $http = new HTTPConnection();
        $this->assertInstanceOf("RDev\\Models\\Web\\Request", $http->getRequest());
    }

    /**
     * Tests getting the HTTP response
     */
    public function testGettingResponse()
    {
        $http = new HTTPConnection();
        $this->assertInstanceOf("RDev\\Models\\Web\\Response", $http->getResponse());
    }

    /**
     * Tests setting the HTTP response
     */
    public function testSettingResponse()
    {
        $http = new HTTPConnection();
        $response = new Response();
        $http->setResponse($response);
        $this->assertSame($response, $http->getResponse());
    }
} 