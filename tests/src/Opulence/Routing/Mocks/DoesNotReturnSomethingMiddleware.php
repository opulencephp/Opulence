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

/**
 * Mocks middleware that does not return something
 */
class DoesNotReturnSomethingMiddleware implements IMiddleware
{
    /**
     * @inheritdoc
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}