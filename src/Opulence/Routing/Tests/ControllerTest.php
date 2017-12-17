<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Tests;

use Opulence\Http\Requests\Request;
use Opulence\Routing\Controller;

/**
 * Tests the controller
 */
class ControllerTest extends \PHPUnit\Framework\TestCase
{
    /** @var Controller The controller to use in tests */
    private $controller = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->controller = new Controller();
        $this->controller->setRequest(Request::createFromGlobals());
    }

    /**
     * Tests getting the view
     */
    public function testGettingView() : void
    {
        $this->assertNull($this->controller->getView());
    }
}
