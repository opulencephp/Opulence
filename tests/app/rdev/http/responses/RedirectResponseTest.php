<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the redirect response class
 */
namespace RDev\HTTP;

class RedirectResponseTest extends \PHPUnit_Framework_TestCase
{
    /** @var RedirectResponse The response to use in tests */
    private $redirectResponse = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->redirectResponse = new RedirectResponse("/foo", ResponseHeaders::HTTP_ACCEPTED, ["HTTP_FOO" => "bar"]);
    }

    /**
     * Tests getting the headers after setting them in the constructor
     */
    public function testGettingHeadersAfterSettingInConstructor()
    {
        $this->assertEquals([
            "FOO" => ["bar"],
            "Location" => ["/foo"]
        ], $this->redirectResponse->getHeaders()->getAll());
    }

    /**
     * Tests getting the status code after setting it in the constructor
     */
    public function testGettingStatusCodeAfterSettingInConstructor()
    {
        $this->assertEquals(ResponseHeaders::HTTP_ACCEPTED, $this->redirectResponse->getStatusCode());
    }

    /**
     * Tests getting the target URL after setting it in the constructor
     */
    public function testGettingTargetURLAfterSettingInConstructor()
    {
        $this->assertEquals("/foo", $this->redirectResponse->getTargetURL());
        $this->assertEquals("/foo", $this->redirectResponse->getHeaders()->get("Location"));
    }

    /**
     * Tests setting the target URL
     */
    public function testSettingTargetURL()
    {
        $this->redirectResponse->setTargetURL("/bar");
        $this->assertEquals("/bar", $this->redirectResponse->getTargetURL());
        $this->assertEquals("/bar", $this->redirectResponse->getHeaders()->get("Location"));
    }
} 