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
    private ClassContainerBinding $binding;

    protected function setUp(): void
    {
        $this->binding = new ClassContainerBinding('foo', ['bar'], false);
    }

    public function testCheckingIfShouldResolveAsSingleton(): void
    {
        $singletonBinding = new ClassContainerBinding('foo', [], true);
        $prototypeBinding = new ClassContainerBinding('foo', [], false);
        $this->assertTrue($singletonBinding->resolveAsSingleton());
        $this->assertFalse($prototypeBinding->resolveAsSingleton());
    }

    public function testGettingConcreteClass(): void
    {
        $this->assertEquals('foo', $this->binding->getConcreteClass());
    }

    public function testGettingConstructorPrimitives(): void
    {
        $this->assertEquals(['bar'], $this->binding->getConstructorPrimitives());
    }
}
