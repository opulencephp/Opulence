<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Tests;

use Opulence\Ioc\ClassBinding;

/**
 * Tests the class binding
 */
class ClassBindingTest extends \PHPUnit\Framework\TestCase
{
    /** @var ClassBinding The binding to use in tests */
    private $binding = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->binding = new ClassBinding('foo', ['bar'], false);
    }

    /**
     * Tests checking if the binding returns the correct flag for resolving as a singleton
     */
    public function testCheckingIfShouldResolveAsSingleton()
    {
        $singletonBinding = new ClassBinding('foo', [], true);
        $prototypeBinding = new ClassBinding('foo', [], false);
        $this->assertTrue($singletonBinding->resolveAsSingleton());
        $this->assertFalse($prototypeBinding->resolveAsSingleton());
    }

    /**
     * Tests getting the concrete class
     */
    public function testGettingConcreteClass()
    {
        $this->assertEquals('foo', $this->binding->getConcreteClass());
    }

    /**
     * Tests getting the constructor primitives
     */
    public function testGettingConstructorPrimitives()
    {
        $this->assertEquals(['bar'], $this->binding->getConstructorPrimitives());
    }
}
