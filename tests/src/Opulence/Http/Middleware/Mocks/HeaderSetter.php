<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Http\Middleware\Mocks;

use Closure;
use Opulence\Http\Middleware\IMiddleware;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;

/**
 * Mocks a middleware that writes to the response's headers
 */
class HeaderSetter implements IMiddleware
{
    /**
     * @inheritdoc
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);
        $response->getHeaders()->add("foo", "bar");

        return $response;
    }
}