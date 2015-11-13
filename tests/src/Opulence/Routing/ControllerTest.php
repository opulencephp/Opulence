<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Routing;

use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;

/**
 * Tests the controller
 */
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
    public function testShowingHttpError()
    {
        $response = $this->controller->showHttpError(ResponseHeaders::HTTP_NOT_FOUND);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEmpty($response->getContent());
        $this->assertEquals(ResponseHeaders::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}