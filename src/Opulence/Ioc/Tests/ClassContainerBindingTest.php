<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Tests;

use Opulence\Ioc\ClassContainerBinding;

/**
 * Tests the class container binding
 */
class ClassContainerBindingTest extends \PHPUnit\Framework\TestCase
{
    /** @var ClassContainerBinding The binding to use in tests */
    private $binding;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->binding = new ClassContainerBinding('foo', ['bar'], false);
    }

    /**
     * Tests checking if the binding returns the correct flag for resolving as a singleton
     */
    public function testCheckingIfShouldResolveAsSingleton(): void
    {
        $singletonBinding = new ClassContainerBinding('foo', [], true);
        $prototypeBinding = new ClassContainerBinding('foo', [], false);
        $this->assertTrue($singletonBinding->resolveAsSingleton());
        $this->assertFalse($prototypeBinding->resolveAsSingleton());
    }

    /**
     * Tests getting the concrete class
     */
    public function testGettingConcreteClass(): void
    {
        $this->assertEquals('foo', $this->binding->getConcreteClass());
    }

    /**
     * Tests getting the constructor primitives
     */
    public function testGettingConstructorPrimitives(): void
    {
        $this->assertEquals(['bar'], $this->binding->getConstructorPrimitives());
    }
}
