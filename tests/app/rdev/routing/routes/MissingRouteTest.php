<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the missing route
 */
namespace RDev\Routing\Routes;
use RDev\HTTP\Responses\ResponseHeaders;

class MissingRouteTest extends \PHPUnit_Framework_TestCase
{
    /** @var MissingRoute The missing route to use in tests */
    private $missingRoute = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->missingRoute = new MissingRoute("MyApp\\MyController");
    }

    /**
     * Tests getting the controller options
     */
    public function testGettingControllerOptions()
    {
        $this->assertEquals("MyApp\\MyController", $this->missingRoute->getControllerName());
        $this->assertEquals("showHTTPError", $this->missingRoute->getControllerMethod());
    }

    /**
     * Tests getting the controller options for a custom missing route
     */
    public function testGettingControllerOptionsForCustomMissingRoute()
    {
        $customMissingRoute = new MissingRoute("MyApp\\MyCustomController", "customMethod");
        $this->assertEquals("MyApp\\MyCustomController", $customMissingRoute->getControllerName());
        $this->assertEquals("customMethod", $customMissingRoute->getControllerMethod());
    }

    /**
     * Tests getting the default value
     */
    public function testGettingDefaultValue()
    {
        $this->assertEquals(ResponseHeaders::HTTP_NOT_FOUND, $this->missingRoute->getDefaultValue("statusCode"));
    }
}