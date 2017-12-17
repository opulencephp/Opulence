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
    public function setUp() : void
    {
        $this->parameters = new MiddlewareParameters('foo', ['bar' => 'baz']);
    }

    /**
     * Tests getting the middleware class name
     */
    public function testGettingMiddlewareClassName() : void
    {
        $this->assertEquals('foo', $this->parameters->getMiddlewareClassName());
    }

    /**
     * Tests getting the parameters
     */
    public function testGettingParameters() : void
    {
        $this->assertEquals(['bar' => 'baz'], $this->parameters->getParameters());
    }
}
