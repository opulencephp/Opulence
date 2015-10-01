<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the controller
 */
namespace Opulence\Routing;

use Opulence\HTTP\Requests\Request;
use Opulence\HTTP\Responses\Response;
use Opulence\HTTP\Responses\ResponseHeaders;

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
     * Tests getting the view
     */
    public function testGettingView()
    {
        $this->assertNull($this->controller->getView());
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