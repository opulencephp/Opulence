<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Routing\Dispatchers;

use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Pipelines\Pipeline;
use Opulence\Pipelines\PipelineException;
use Opulence\Routing\RouteException;

/**
 * Defines the middleware dispatcher that sends requests through a pipeline
 */
class MiddlewarePipeline implements IMiddlewarePipeline
{
    /**
     * @inheritdoc
     */
    public function send(Request $request, array $middleware, callable $controller) : Response
    {
        try {
            $response = (new Pipeline)
                ->send($request)
                ->through($middleware, 'handle')
                ->then($controller)
                ->execute();

            return $response ?? new Response();
        } catch (PipelineException $ex) {
            throw new RouteException('Failed to send request through middleware pipeline', 0, $ex);
        }
    }
}
