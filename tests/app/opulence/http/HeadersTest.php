<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the headers class
 */
namespace Opulence\HTTP;

class HeadersTest extends \PHPUnit_Framework_TestCase
{
    /** @var Headers The headers to use in tests */
    private $headers = null;
    /** @var array The server array to use */
    private $serverArray = [
        "NON_HEADER" => "foo",
        "CONTENT_LENGTH" => 4,
        "CONTENT_TYPE" => "foo",
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
     * Tests setting a string value
     */
    public function testAddingStringValue()
    {
        $this->headers->add("foo", "bar");
        $this->assertEquals("bar", $this->headers->get("foo"));
    }

    /**
     * Tests getting all the headers after setting them in the constructor
     */
    public function testGettingAllAfterSettingInConstructor()
    {
        $headerParameters = [];

        foreach ($this->serverArray as $key => $value) {
            if (strpos($key, "HTTP_") === 0) {
                if (!is_array($value)) {
                    $value = [$value];
                }

                $headerParameters[substr($key, 5)] = $value;
            } elseif (strpos($key, "CONTENT_") === 0) {
                if (!is_array($value)) {
                    $value = [$value];
                }

                $headerParameters[$key] = $value;
            }
        }

        $this->assertEquals($headerParameters, $this->headers->getAll());
    }

    /**
     * Tests returning all the values
     */
    public function testGettingAllValues()
    {
        $this->assertEquals(["host"], $this->headers->get("HOST", null, false));
    }

    /**
     * Tests returning all the values when the key does not exist
     */
    public function testGettingAllValuesWhenKeyDoesNotExist()
    {
        $this->assertEquals("foo", $this->headers->get("THIS_DOES_NOT_EXIST", "foo", false));
    }

    /**
     * Tests returning only the first value
     */
    public function testGettingFirstValue()
    {
        $this->assertEquals("host", $this->headers->get("HOST", null, true));
    }

    /**
     * Tests returning only the first value when the key does not exist
     */
    public function testGettingFirstValueWhenKeyDoesNotExist()
    {
        $this->assertEquals("foo", $this->headers->get("THIS_DOES_NOT_EXIST", "foo", true));
    }
} 