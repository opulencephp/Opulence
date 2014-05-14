<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the API response class
 */
namespace RamODev\Application\Shared\Models\Web\API;
use RamODev\Application\Shared\Models\Web;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the output for a response that doesn't have an output set
     */
    public function testGettingEmptyOutputResponse()
    {
        $response = new Response(Web\Response::HTTP_BAD_REQUEST);
        $this->assertEquals("", $response->getOutput());
    }

    /**
     * Tests getting the HTTP response code
     */
    public function testGettingHTTPResponseCode()
    {
        $httpResponseCode = Web\Response::HTTP_FORBIDDEN;
        $response = new Response($httpResponseCode);
        $this->assertEquals($httpResponseCode, $response->getHTTPResponseCode());
    }

    /**
     * Tests getting the output
     */
    public function testGettingOutput()
    {
        $output = "foobar";
        $response = new Response(Web\Response::HTTP_BAD_REQUEST, $output);
        $this->assertEquals($output, $response->getOutput());
    }

    /**
     * Tests setting the HTTP response code
     */
    public function testSettingHTTPResponseCode()
    {
        $httpResponseCode = Web\Response::HTTP_FORBIDDEN;
        $response = new Response(Web\Response::HTTP_OK);
        $response->setHTTPResponseCode($httpResponseCode);
        $this->assertEquals($httpResponseCode, $response->getHTTPResponseCode());
    }

    /**
     * Tests setting the output
     */
    public function testSettingOutput()
    {
        $output = "foobar";
        $response = new Response(Web\Response::HTTP_BAD_REQUEST);
        $response->setOutput($output);
        $this->assertEquals($output, $response->getOutput());
    }
} 