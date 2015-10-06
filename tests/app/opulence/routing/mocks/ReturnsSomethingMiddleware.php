<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks middleware that returns something
 */
namespace Opulence\Tests\Routing\Mocks;

use Closure;
use Opulence\HTTP\Middleware\IMiddleware;
use Opulence\HTTP\Requests\Request;
use Opulence\HTTP\Responses\RedirectResponse;
use Opulence\HTTP\Responses\Response;

class ReturnsSomethingMiddleware implements IMiddleware
{
    /**
     * @inheritdoc
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        if ($response !== null) {
            $response->setContent($response->getContent() . ":something");

            return $response;
        }else {
            return new RedirectResponse("/bar");
        }
    }
}