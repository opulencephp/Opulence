<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the HTTP connection class
 */
namespace RDev\HTTP;

class HTTPConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the HTTP request
     */
    public function testGettingRequest()
    {
        $http = new Connection();
        $this->assertInstanceOf("RDev\\HTTP\\Request", $http->getRequest());
    }

    /**
     * Tests getting the HTTP response
     */
    public function testGettingResponse()
    {
        $http = new Connection();
        $this->assertInstanceOf("RDev\\HTTP\\Response", $http->getResponse());
    }

    /**
     * Tests setting the HTTP response
     */
    public function testSettingResponse()
    {
        $http = new Connection();
        $response = new Response();
        $http->setResponse($response);
        $this->assertSame($response, $http->getResponse());
    }
} 