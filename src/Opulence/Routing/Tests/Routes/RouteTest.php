<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Tests\Routes;

use Closure;
use InvalidArgumentException;
use Opulence\Routing\Middleware\MiddlewareParameters;
use Opulence\Routing\Routes\Route;
use Opulence\Routing\Tests\Middleware\Mocks\ParameterizedMiddleware;

/**
 * Tests the route class
 */
class RouteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests adding multiple middleware
     */
    public function testAddingMultipleMiddleware() : void
    {
        $route = new Route('get', '/{foo}', 'foo@bar');
        $route->addMiddleware(['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $route->getMiddleware());
    }

    /**
     * Tests adding non-unique middleware
     */
    public function testAddingNonUniqueMiddleware() : void
    {
        $route = new Route('get', '/{foo}', 'foo@bar');
        $route->addMiddleware('foo');
        $route->addMiddleware('foo');
        $this->assertEquals(['foo'], $route->getMiddleware());
    }

    /**
     * Tests adding non-unique parameterized middleware
     */
    public function testAddingNonUniqueParameterizedMiddleware() : void
    {
        $route = new Route('get', '/{foo}', 'foo@bar');
        $route->addMiddleware(ParameterizedMiddleware::withParameters(['foo' => 'bar']));
        $route->addMiddleware(ParameterizedMiddleware::withParameters(['foo' => 'bar']));
        $this->assertEquals([ParameterizedMiddleware::withParameters(['foo' => 'bar'])], $route->getMiddleware());
    }

    /**
     * Tests adding a single middleware
     */
    public function testAddingSingleMiddleware() : void
    {
        $route = new Route('get', '/{foo}', 'foo@bar');
        $route->addMiddleware('foo');
        $this->assertEquals(['foo'], $route->getMiddleware());
    }

    /**
     * Tests that a route that uses a controller does not say it's using a closure
     */
    public function testControllerRouteDoesNotSayItsUsingClosure() : void
    {
        $route = new Route('get', '/{foo}', 'foo@bar');
        $this->assertFalse($route->usesCallable());
        $this->assertEquals('foo@bar', $route->getController());
    }

    /**
     * Tests getting the controller method
     */
    public function testGettingControllerMethod() : void
    {
        $route = new Route('get', '/{foo}', 'foo@bar');
        $this->assertEquals('bar', $route->getControllerMethod());
    }

    /**
     * Tests getting the controller name
     */
    public function testGettingControllerName() : void
    {
        $route = new Route('get', '/{foo}', 'foo@bar');
        $this->assertEquals('foo', $route->getControllerName());
        $this->assertEquals('foo@bar', $route->getController());
    }

    /**
     * Tests getting the methods
     */
    public function testGettingMethods() : void
    {
        $route = new Route('get', '/foo', 'foo@bar');
        $this->assertEquals(['get'], $route->getMethods());
    }

    /**
     * Tests getting the middleware when it is a string
     */
    public function testGettingMiddlewareWhenItIsAString() : void
    {
        $options = [
            'middleware' => 'foo'
        ];
        $route = new Route('get', '/foo', 'foo@bar', $options);
        $this->assertEquals(['foo'], $route->getMiddleware());
    }

    /**
     * Tests getting the middleware when it is an object
     */
    public function testGettingMiddlewareWhenItIsAnObject() : void
    {
        $options = [
            'middleware' => new MiddlewareParameters('foo', ['bar' => 'baz'])
        ];
        $route = new Route('get', '/foo', 'foo@bar', $options);
        $this->assertEquals([$options['middleware']], $route->getMiddleware());
    }

    /**
     * Tests getting the middleware when they are an array
     */
    public function testGettingMiddlewareWhenTheyAreAnArray() : void
    {
        $options = [
            'middleware' => ['foo', 'bar']
        ];
        $route = new Route('get', '/foo', 'foo@bar', $options);
        $this->assertEquals(['foo', 'bar'], $route->getMiddleware());
    }

    /**
     * Tests getting the raw path
     */
    public function testGettingRawPath() : void
    {
        $route = new Route('get', '/foo/{id}', 'foo@bar');
        $this->assertEquals('/foo/{id}', $route->getRawPath());
    }

    /**
     * Tests getting an unset name
     */
    public function testGettingUnsetName() : void
    {
        $route = new Route('get', '/{foo}', 'foo@bar');
        $this->assertEmpty($route->getName());
    }

    /**
     * Tests getting the regex for a variable
     */
    public function testGettingVariableRegex() : void
    {
        $options = [
            'vars' => ['bar' => "\d+"]
        ];
        $route = new Route('get', '/foo', 'foo@bar', $options);
        $this->assertEquals("\d+", $route->getVarRegex('bar'));
    }

    /**
     * Tests getting the regex for a variable that does not have a regex
     */
    public function testGettingVariableRegexForParameterWithNoRegex() : void
    {
        $route = new Route('get', '/foo', 'foo@bar');
        $this->assertNull($route->getVarRegex('bar'));
    }

    /**
     * Tests not setting HTTPS
     */
    public function testNotSettingHttps() : void
    {
        $route = new Route('get', '/{foo}', 'foo@bar');
        $this->assertFalse($route->isSecure());
    }

    /**
     * Tests passing in a controller with no method
     */
    public function testPassingControllerWithNoMethod() : void
    {
        $this->expectException(InvalidArgumentException::class);
        new Route('get', '/{foo}', 'foo@');
    }

    /**
     * Tests passing in a controller with no name
     */
    public function testPassingControllerWithNoName() : void
    {
        $this->expectException(InvalidArgumentException::class);
        new Route('get', '/{foo}', '@bar');
    }

    /**
     * Tests passing in an incorrectly formatted controller
     */
    public function testPassingIncorrectlyFormattedController() : void
    {
        $this->expectException(InvalidArgumentException::class);
        new Route('get', '/{foo}', 'foo');
    }

    /**
     * Tests passing in multiple methods to the constructor
     */
    public function testPassingMultipleMethodsToConstructor() : void
    {
        $route = new Route(['get', 'post'], '/foo', 'foo@bar');
        $this->assertEquals(['get', 'post'], $route->getMethods());
    }

    /**
     * Tests passing in a single method to the constructor
     */
    public function testPassingSingleMethodToConstructor() : void
    {
        $route = new Route('get', '/foo', 'foo@bar');
        $this->assertEquals(['get'], $route->getMethods());
    }

    /**
     * Test prepending a middleware
     */
    public function testPrependingMiddleware() : void
    {
        $route = new Route('get', '/{foo}', 'foo@bar');
        $route->addMiddleware('foo', false);
        $route->addMiddleware('bar', true);
        $this->assertEquals(['bar', 'foo'], $route->getMiddleware());
    }

    /**
     * Tests setting the controller closure
     */
    public function testSettingControllerClosure() : void
    {
        $route = new Route('get', '/', 'foo@bar');
        $route->setControllerCallable(function () {
        });
        $this->assertInstanceOf(Closure::class, $route->getController());
        $this->assertTrue($route->usesCallable());
    }

    /**
     * Tests setting the controller method
     */
    public function testSettingControllerMethod() : void
    {
        $route = new Route('get', '/{foo}', 'foo@bar');
        $route->setControllerMethod('blah');
        $this->assertEquals('blah', $route->getControllerMethod());
        $this->assertEquals('foo@blah', $route->getController());
    }

    /**
     * Tests setting the controller name
     */
    public function testSettingControllerName() : void
    {
        $route = new Route('get', '/{foo}', 'foo@bar');
        $route->setControllerName('blah');
        $this->assertEquals('blah', $route->getControllerName());
        $this->assertEquals('blah@bar', $route->getController());
    }

    /**
     * Tests setting HTTPS
     */
    public function testSettingHttps() : void
    {
        $options = [
            'https' => false
        ];
        $route = new Route('get', '/{foo}', 'foo@bar', $options);
        $this->assertFalse($route->isSecure());
        $options = [
            'https' => true
        ];
        $route = new Route('get', '/{foo}', 'foo@bar', $options);
        $this->assertTrue($route->isSecure());
    }

    /**
     * Tests setting multiple variable regexes
     */
    public function testSettingMultipleVariableRegexes() : void
    {
        $route = new Route('get', '/{foo}/{bar}', 'foo@bar');
        $regexes = ['foo' => "\d+", 'bar' => "\w+"];
        $route->setVarRegexes($regexes);
        $this->assertEquals($regexes, $route->getVarRegexes());
    }

    /**
     * Tests setting the name
     */
    public function testSettingName() : void
    {
        $route = new Route('get', '/{foo}', 'foo@bar');
        $route->setName('blah');
        $this->assertEquals('blah', $route->getName());
    }

    /**
     * Tests setting the name in the constructor
     */
    public function testSettingNameInConstructor() : void
    {
        $options = [
            'name' => 'blah'
        ];
        $route = new Route('get', '/{foo}', 'foo@bar', $options);
        $this->assertEquals('blah', $route->getName());
    }

    /**
     * Tests setting the raw host
     */
    public function testSettingRawHost() : void
    {
        $route = new Route('get', '/foo', 'foo@bar');
        $route->setRawHost('google.com');
        $this->assertEquals('google.com', $route->getRawHost());
    }

    /**
     * Tests setting the raw host in the constructor
     */
    public function testSettingRawHostInConstructor() : void
    {
        $options = [
            'host' => 'google.com'
        ];
        $route = new Route('get', '/foo', 'foo@bar', $options);
        $this->assertEquals('google.com', $route->getRawHost());
    }

    /**
     * Tests setting the raw path
     */
    public function testSettingRawPath() : void
    {
        $route = new Route('get', '/{foo}', 'foo@bar');
        $route->setRawPath('blah');
        $this->assertEquals('blah', $route->getRawPath());
    }

    /**
     * Tests setting a variable regex
     */
    public function testSettingVariableRegex() : void
    {
        $route = new Route('get', '/{foo}', 'foo@bar');
        $route->setVarRegex('foo', "\d+");
        $this->assertEquals(['foo' => "\d+"], $route->getVarRegexes());
        $this->assertEquals("\d+", $route->getVarRegex('foo'));
    }

    /**
     * Tests using a closure
     */
    public function testUsingClosure() : void
    {
        $closure = function () {
            return 'foo';
        };
        $route = new Route('get', '/{foo}', $closure);
        $this->assertTrue($route->usesCallable());
        $this->assertEquals($closure, $route->getController());
    }
}
