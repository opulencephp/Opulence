<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Routing\Tests\Middleware\Mocks;

use Closure;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Middleware\IMiddleware;

/**
 * Mocks middleware that does not return something
 */
class DoesNotReturnSomethingMiddleware implements IMiddleware
{
    /**
     * @inheritdoc
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
