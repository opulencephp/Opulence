<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Routing\Tests\Middleware;

use Opulence\Routing\Middleware\MiddlewareParameters;
use Opulence\Routing\Tests\Middleware\Mocks\ParameterizedMiddleware as ParameterizedMiddlewareMock;

/**
 * Tests the parameterized middleware
 */
class ParameterizedMiddlewareTest extends \PHPUnit\Framework\TestCase
{
    /** @var ParameterizedMiddlewareMock The middleware to use in tests */
    private $middleware;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->middleware = new ParameterizedMiddlewareMock();
    }

    /**
     * Tests that middleware parameters are created correctly
     */
    public function testWithCreatesMiddlewareParametersCorrectly(): void
    {
        /** @var MiddlewareParameters $parameters */
        $parameters = ParameterizedMiddlewareMock::withParameters(['bar' => 'baz']);
        $this->assertInstanceOf(MiddlewareParameters::class, $parameters);
        $this->assertEquals(ParameterizedMiddlewareMock::class, $parameters->getMiddlewareClassName());
        $this->assertEquals(['bar' => 'baz'], $parameters->getParameters());
    }
}
