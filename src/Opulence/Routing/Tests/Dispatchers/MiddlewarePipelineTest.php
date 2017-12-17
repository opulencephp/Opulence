<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Tests\Dispatchers;

use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Opulence\Routing\Dispatchers\MiddlewarePipeline;
use Opulence\Routing\Tests\Middleware\Mocks\ReturnsSomethingMiddleware;

/**
 * Tests the pipeline middleware pipeline
 */
class MiddlewarePipelineTest extends \PHPUnit\Framework\TestCase
{
    /** @var MiddlewarePipeline The middleware pipeline */
    private $middlewarePipeline = null;

    /**
     * Sets up tests
     */
    public function setUp() : void
    {
        $this->middlewarePipeline = new MiddlewarePipeline();
    }

    /**
     * Tests that an empty response is returned when no response is otherwise returned
     */
    public function testEmptyResponseReturnedWhenNoResponseIsOtherwiseReturned() : void
    {
        $controller = function () {
            // Don't do anything
        };
        $response = $this->middlewarePipeline->send(Request::createFromGlobals(), [], $controller);
        /** @var Response $response */
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEmpty($response->getContent());
        $this->assertEquals(ResponseHeaders::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Tests that middleware can affect the response
     */
    public function testMiddlewareCanAffectResponse() : void
    {
        $middleware = [
            new ReturnsSomethingMiddleware(),
            new ReturnsSomethingMiddleware()
        ];
        $controller = function () {
            return new Response('foo');
        };
        $response = $this->middlewarePipeline->send(Request::createFromGlobals(), $middleware, $controller);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('foo:something:something', $response->getContent());
        $this->assertEquals(ResponseHeaders::HTTP_OK, $response->getStatusCode());
    }
}
