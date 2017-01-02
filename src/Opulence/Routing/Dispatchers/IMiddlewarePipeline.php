<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Routing\Dispatchers;

use Opulence\Http\Requests\IMiddleware;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Routing\RouteException;

/**
 * Defines the interface for middleware pipelines to implement
 */
interface IMiddlewarePipeline
{
    /**
     * Sends the request through a series of middleware
     *
     * @param Request $request The request to send
     * @param IMiddleware[] $middleware The list of middleware to send the request through
     * @param callable $controller The controller to call
     * @return Response The resulting response
     * @return mixed The result of the pipeline
     * @throws RouteException Thrown if there was a problem sending the request through the pipeline
     */
    public function send(Request $request, array $middleware, callable $controller) : Response;
}