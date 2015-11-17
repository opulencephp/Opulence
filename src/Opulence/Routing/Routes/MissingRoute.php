<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Routing\Routes;

use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\ResponseHeaders;

/**
 * Defines a route that is dispatched when the router misses on a path
 */
class MissingRoute extends CompiledRoute
{
    /**
     * @param string $controllerClass The name of the controller to call
     * @param string $controllerMethod The name of the controller method
     */
    public function __construct($controllerClass, $controllerMethod = "showHttpError")
    {
        $methods = [
            Request::METHOD_DELETE,
            Request::METHOD_GET,
            Request::METHOD_POST,
            Request::METHOD_PUT,
            Request::METHOD_HEAD,
            Request::METHOD_TRACE,
            Request::METHOD_PURGE,
            Request::METHOD_CONNECT,
            Request::METHOD_PATCH,
            Request::METHOD_OPTIONS
        ];
        $route = new Route($methods, "", "$controllerClass@$controllerMethod");
        parent::__construct(new ParsedRoute($route), true);

        $this->setDefaultValue("statusCode", ResponseHeaders::HTTP_NOT_FOUND);
    }
}