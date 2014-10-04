<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the headers class
 */
namespace RDev\Models\HTTP;

class HeadersTest extends \PHPUnit_Framework_TestCase
{
    /** @var Headers The headers to use in tests */
    private $headers = null;
    /** @var array The server array to use */
    private $serverArray = [
        "NON_HEADER" => "foo",
        "HTTP_ACCEPT" => "accept",
        "HTTP_ACCEPT_CHARSET" => "accept_charset",
        "HTTP_ACCEPT_ENCODING" => "accept_encoding",
        "HTTP_ACCEPT_LANGUAGE" => "accept_language",
        "HTTP_CONNECTION" => "connection",
        "HTTP_HOST" => "host",
        "HTTP_REFERER" => "referer",
        "HTTP_USER_AGENT" => "user_agent"
    ];

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->headers = new Headers($this->serverArray);
    }

    /**
     * Tests getting all the headers
     */
    public function testGettingAll()
    {
        $headerParameters = [];

        foreach($this->serverArray as $key => $value)
        {
            if(strpos($key, "HTTP_") === 0)
            {
                $headerParameters[substr($key, 5)] = $value;
            }
        }

        $this->assertEquals($headerParameters, $this->headers->getAll());
    }
} 