<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Routing\Tests\Routes;

use Opulence\Routing\Routes\CompiledRoute;
use Opulence\Routing\Routes\ParsedRoute;
use Opulence\Routing\Routes\Route;

/**
 * Tests the compiled route
 */
class CompiledRouteTest extends \PHPUnit\Framework\TestCase
{
    /** @var CompiledRoute The route to test */
    private $compiledRoute = null;
    /** @var ParsedRoute The parsed route */
    private $parsedRoute = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->parsedRoute = new ParsedRoute(new Route('GET', '/', 'foo@bar'));
        $this->compiledRoute = new CompiledRoute($this->parsedRoute, true, ['foo' => 'bar']);
    }

    /**
     * Tests checking if a matched route is matched
     */
    public function testCheckingIfMatched()
    {
        $this->assertTrue($this->compiledRoute->isMatch());
    }

    /**
     * Tests creating a compiled route
     */
    public function testCreatingCompiledRoute()
    {
        $route = new Route('GET', '/foo/{bar=baz}', 'foo@bar', [
            'https' => true,
            'vars' => [
                'bar' => "\d+"
            ]
        ]);
        $parsedRoute = new ParsedRoute($route);
        $parsedRoute->setDefaultValue('bar', 'baz');
        $parsedRoute->setHostRegex("foo\.bar\.com");
        $parsedRoute->setPathRegex('baz.*');
        $compiledRoute = new CompiledRoute($parsedRoute, true, []);
        $this->assertEquals($parsedRoute->getHostRegex(), $compiledRoute->getHostRegex());
        $this->assertEquals($parsedRoute->getPathRegex(), $compiledRoute->getPathRegex());
        $this->assertEquals($parsedRoute->getDefaultValue('bar'), $compiledRoute->getDefaultValue('bar'));
    }

    /**
     * Tests getting a non-existent path variable
     */
    public function testGettingNonExistentPathVariable()
    {
        $this->assertNull($this->compiledRoute->getPathVar('doesNotExist'));
    }

    /**
     * Tests getting a single path variable
     */
    public function testGettingPathVariable()
    {
        $this->assertEquals('bar', $this->compiledRoute->getPathVar('foo'));
    }

    /**
     * Tests getting the path variables
     */
    public function testGettingPathVariables()
    {
        $this->assertEquals(['foo' => 'bar'], $this->compiledRoute->getPathVars());
    }

    /**
     * Tests not specifying path variables
     */
    public function testNotSpecifyingPathVariables()
    {
        $compiledRoute = new CompiledRoute($this->parsedRoute, true);
        $this->assertEquals([], $compiledRoute->getPathVars());
    }

    /**
     * Tests setting the match
     */
    public function testSettingMatch()
    {
        $this->compiledRoute->setMatch(false);
        $this->assertFalse($this->compiledRoute->isMatch());
    }

    /**
     * Tests setting the path variables
     */
    public function testSettingPathVariables()
    {
        $this->compiledRoute->setPathVars(['dave' => 'young']);
        $this->assertEquals(['dave' => 'young'], $this->compiledRoute->getPathVars());
    }
}
