<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Tests\Middleware;

use Opulence\Routing\Middleware\MiddlewareParameters;

/**
 * Tests the middleware parameters
 */
class MiddlewareParametersTest extends \PHPUnit\Framework\TestCase
{
    /** @var MiddlewareParameters The parameters to use in tests */
    private $parameters = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->parameters = new MiddlewareParameters('foo', ['bar' => 'baz']);
    }

    /**
     * Tests getting the middleware class name
     */
    public function testGettingMiddlewareClassName()
    {
        $this->assertEquals('foo', $this->parameters->getMiddlewareClassName());
    }

    /**
     * Tests getting the parameters
     */
    public function testGettingParameters()
    {
        $this->assertEquals(['bar' => 'baz'], $this->parameters->getParameters());
    }
}
