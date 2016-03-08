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
use Opulence\Http\Middleware\ParameterizedMiddleware as Base;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;

/**
 * Mocks the parameterized middleware for use in tests
 */
class ParameterizedMiddleware extends Base
{
    /**
     * @inheritdoc
     */
    public function handle(Request $request, Closure $next) : Response
    {
        /** @var Response $response */
        $response = $next($request);
        $response->getHeaders()->set("parameterized", "middleware");
        $response->getHeaders()->set("parameters", $this->parameters);

        return $response;
    }
}