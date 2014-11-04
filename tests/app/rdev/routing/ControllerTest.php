<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the controller
 */
namespace RDev\Routing;
use RDev\HTTP;

class ControllerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Controller The controller to use in tests */
    private $controller = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->controller = new Controller(new HTTP\Connection());
    }

    /**
     * Tests showing an HTTP error
     */
    public function testShowingHTTPError()
    {
        $response = $this->controller->showHTTPError(HTTP\ResponseHeaders::HTTP_NOT_FOUND);
        $this->assertInstanceOf("RDev\\HTTP\\Response", $response);
        $this->assertEmpty($response->getContent());
        $this->assertEquals(HTTP\ResponseHeaders::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}