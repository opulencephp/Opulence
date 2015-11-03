<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Routing\Routes;

use Opulence\Http\Requests;
use Opulence\Http\Responses;

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
            Requests\Request::METHOD_DELETE,
            Requests\Request::METHOD_GET,
            Requests\Request::METHOD_POST,
            Requests\Request::METHOD_PUT,
            Requests\Request::METHOD_HEAD,
            Requests\Request::METHOD_TRACE,
            Requests\Request::METHOD_PURGE,
            Requests\Request::METHOD_CONNECT,
            Requests\Request::METHOD_PATCH,
            Requests\Request::METHOD_OPTIONS
        ];
        $route = new Route($methods, "", "{$controllerClass}@{$controllerMethod}");
        parent::__construct(new ParsedRoute($route), true);

        $this->setDefaultValue("statusCode", Responses\ResponseHeaders::HTTP_NOT_FOUND);
    }
}