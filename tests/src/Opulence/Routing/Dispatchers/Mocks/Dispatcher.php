<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Routing\Dispatchers\Mocks;

use Opulence\Http\Requests\Request;
use Opulence\Routing\Controller;
use Opulence\Routing\Dispatchers\Dispatcher as BaseDispatcher;
use Opulence\Routing\Routes\CompiledRoute;
use Opulence\Routing\Routes\Route;

/**
 * Mocks the dispatcher for use in testing
 */
class Dispatcher extends BaseDispatcher
{
    /**
     * For the sake of testing the router, simply return the dispatched route rather than its output
     * This makes it easy to test that the router is selecting the correct route
     *
     * @param CompiledRoute $route The route to be dispatched
     * @param Request $request The request made by the user
     * @param Controller|mixed|null $controller Will be set to the instance of the controller that was matched
     * @return Route The chosen route
     */
    public function dispatch(CompiledRoute $route, Request $request, &$controller = null)
    {
        $controller = new Controller();

        return $route;
    }
} 