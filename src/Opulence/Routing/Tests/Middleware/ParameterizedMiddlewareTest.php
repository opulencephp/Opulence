<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Routing\Tests\Middleware;

use Opulence\Routing\Middleware\MiddlewareParameters;
use Opulence\Routing\Tests\Middleware\Mocks\ParameterizedMiddleware as ParameterizedMiddlewareMock;

/**
 * Tests the parameterized middleware
 */
class ParameterizedMiddlewareTest extends \PHPUnit\Framework\TestCase
{
    /** @var ParameterizedMiddlewareMock The middleware to use in tests */
    private $middleware = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->middleware = new ParameterizedMiddlewareMock();
    }

    /**
     * Tests that middleware parameters are created correctly
     */
    public function testWithCreatesMiddlewareParametersCorrectly()
    {
        /** @var MiddlewareParameters $parameters */
        $parameters = ParameterizedMiddlewareMock::withParameters(['bar' => 'baz']);
        $this->assertInstanceOf(MiddlewareParameters::class, $parameters);
        $this->assertEquals(ParameterizedMiddlewareMock::class, $parameters->getMiddlewareClassName());
        $this->assertEquals(['bar' => 'baz'], $parameters->getParameters());
    }
}
