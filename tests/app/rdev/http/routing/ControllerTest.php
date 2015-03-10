<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the controller
 */
namespace RDev\HTTP\Routing;
use RDev\HTTP\Requests;
use RDev\HTTP\Responses;

class ControllerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Controller The controller to use in tests */
    private $controller = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->controller = new Controller();
        $this->controller->setRequest(Requests\Request::createFromGlobals());
    }

    /**
     * Tests getting the template
     */
    public function testGettingTemplate()
    {
        $this->assertNull($this->controller->getTemplate());
    }

    /**
     * Tests showing an HTTP error
     */
    public function testShowingHTTPError()
    {
        $response = $this->controller->showHTTPError(Responses\ResponseHeaders::HTTP_NOT_FOUND);
        $this->assertInstanceOf("RDev\\HTTP\\Responses\\Response", $response);
        $this->assertEmpty($response->getContent());
        $this->assertEquals(Responses\ResponseHeaders::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}