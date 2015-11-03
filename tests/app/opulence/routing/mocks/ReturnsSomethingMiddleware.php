<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Routing\Mocks;

use Closure;
use Opulence\Http\Middleware\IMiddleware;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\RedirectResponse;
use Opulence\Http\Responses\Response;

/**
 * Mocks middleware that returns something
 */
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
        } else {
            return new RedirectResponse("/bar");
        }
    }
}