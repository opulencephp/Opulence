<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the controller
 */
namespace RDev\Routing;
use RDev\HTTP\Requests\Request;
use RDev\HTTP\Responses\Response;
use RDev\HTTP\Responses\ResponseHeaders;

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
        $this->controller->setRequest(Request::createFromGlobals());
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
        $response = $this->controller->showHTTPError(ResponseHeaders::HTTP_NOT_FOUND);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEmpty($response->getContent());
        $this->assertEquals(ResponseHeaders::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}