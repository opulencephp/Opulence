<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Tests\Routes;

use Opulence\Routing\Routes\ParsedRoute;
use Opulence\Routing\Routes\Route;

/**
 * Tests the parsed route
 */
class ParsedRouteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests creating a parsed route
     */
    public function testCreatingParsedRoute()
    {
        $route = new Route('GET', '/foo/{bar}', 'foo@bar', [
            'https' => true,
            'vars' => [
                'bar' => "\d+"
            ]
        ]);
        $parsedRoute = new ParsedRoute($route);
        $this->assertEquals($route->getMethods(), $parsedRoute->getMethods());
        $this->assertEquals($route->getControllerName(), $parsedRoute->getControllerName());
        $this->assertEquals($route->getControllerMethod(), $parsedRoute->getControllerMethod());
        $this->assertEquals($route->getController(), $parsedRoute->getController());
        $this->assertEquals($route->usesCallable(), $parsedRoute->usesCallable());
        $this->assertEquals($route->getName(), $parsedRoute->getName());
        $this->assertEquals($route->isSecure(), $parsedRoute->isSecure());
        $this->assertEquals($route->getMiddleware(), $parsedRoute->getMiddleware());
        $this->assertEquals($route->getRawHost(), $parsedRoute->getRawHost());
        $this->assertEquals($route->getRawPath(), $parsedRoute->getRawPath());
        $this->assertEquals($route->getVarRegex('bar'), $parsedRoute->getVarRegex('bar'));
    }

    /**
     * Tests getting the default value for a variable without a default value
     */
    public function testGettingDefaultValueForVariableWithoutDefaultValue()
    {
        $route = new Route('get', '/{foo}', 'foo@bar');
        $parsedRoute = new ParsedRoute($route);
        $this->assertNull($parsedRoute->getDefaultValue('foo'));
    }

    /**
     * Tests getting the host regex when it's not set
     */
    public function testGettingHostRegexWhenNotSet()
    {
        $route = new Route('get', '/foo', 'foo@bar');
        $parsedRoute = new ParsedRoute($route);
        $this->assertEquals('#^.*$#', $parsedRoute->getHostRegex());
    }

    /**
     * Tests setting a default value
     */
    public function testSettingADefaultValue()
    {
        $route = new Route('get', '/{foo}', 'foo@bar');
        $parsedRoute = new ParsedRoute($route);
        $parsedRoute->setDefaultValue('foo', 2);
        $this->assertEquals(2, $parsedRoute->getDefaultValue('foo'));
    }

    /**
     * Tests setting the host regex
     */
    public function testSettingHostRegex()
    {
        $route = new Route('get', '/foo', 'foo@bar');
        $parsedRoute = new ParsedRoute($route);
        $parsedRoute->setHostRegex("#google\.com#");
        $this->assertEquals("#google\.com#", $parsedRoute->getHostRegex());
    }

    /**
     * Tests setting the path regex
     */
    public function testSettingPathRegex()
    {
        $route = new Route('get', '/foo/{id}', 'foo@bar');
        $parsedRoute = new ParsedRoute($route);
        $parsedRoute->setPathRegex('blah');
        $this->assertEquals('blah', $parsedRoute->getPathRegex());
    }
}
