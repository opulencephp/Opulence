<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Http\Middleware;

use Closure;
use Opulence\Framework\Configuration\Config;
use Opulence\Http\HttpException;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Middleware\IMiddleware;

/**
 * Checks if the application is in maintenance mode
 */
class CheckMaintenanceMode implements IMiddleware
{
    /**
     * @inheritdoc
     */
    public function handle(Request $request, Closure $next) : Response
    {
        if (file_exists(Config::get("paths", "tmp.framework.http") . "/down")) {
            throw new HttpException(503, "Down for scheduled maintenance");
        }

        return $next($request);
    }
}
