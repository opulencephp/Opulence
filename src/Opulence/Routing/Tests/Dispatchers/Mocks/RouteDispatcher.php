<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Tests\Dispatchers\Mocks;

use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Controller;
use Opulence\Routing\Dispatchers\RouteDispatcher as BaseDispatcher;
use Opulence\Routing\Routes\CompiledRoute;

/**
 * Mocks the dispatcher for use in testing
 */
class RouteDispatcher extends BaseDispatcher
{
    /** @var CompiledRoute The last route */
    private $lastRoute = null;

    /**
     * For the sake of testing the router, simply store the dispatched route rather than its output
     * This makes it easy to test that the router is selecting the correct route
     *
     * @inheritdoc
     */
    public function dispatch(CompiledRoute $route, Request $request, &$controller = null) : Response
    {
        $controller = new Controller();
        $this->lastRoute = $route;

        return new Response();
    }

    /**
     * Gets the last route for testing purposes
     *
     * @return CompiledRoute
     */
    public function getLastRoute() : CompiledRoute
    {
        return $this->lastRoute;
    }
}
