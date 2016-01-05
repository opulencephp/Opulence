<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Routing\Mocks;

use Opulence\Ioc\Container;
use Opulence\Routing\Router as BaseRouter;
use Opulence\Routing\Routes\Compilers\Compiler;
use Opulence\Routing\Routes\Compilers\Matchers\HostMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\PathMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\SchemeMatcher;
use Opulence\Routing\Routes\Compilers\Parsers\Parser;
use Opulence\Tests\Routing\Dispatchers\Mocks\Dispatcher;

/**
 * Mocks the router for use in testing
 */
class Router extends BaseRouter
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $routeMatchers = [
            new PathMatcher(),
            new HostMatcher(),
            new SchemeMatcher()
        ];
        $parser = new Parser();
        $compiler = new Compiler($routeMatchers);

        parent::__construct(new Dispatcher(new Container()), $compiler, $parser);
    }
} 