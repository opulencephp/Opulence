<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the HTTP class
 */
namespace RamODev\Application\Shared\Web;

class HTTPTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the HTTP request
     */
    public function testGettingRequest()
    {
        $http = new HTTP();
        $this->assertInstanceOf("RamODev\\Application\\Shared\\Web\\Request", $http->getRequest());
    }

    /**
     * Tests getting the HTTP response
     */
    public function testGettingResponse()
    {
        $http = new HTTP();
        $this->assertInstanceOf("RamODev\\Application\\Shared\\Web\\Response", $http->getResponse());
    }
} 