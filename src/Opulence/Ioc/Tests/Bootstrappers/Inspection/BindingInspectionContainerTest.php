<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Tests\Bootstrappers\Inspection;

use Opulence\Ioc\Bootstrappers\Inspection\BindingInspectionContainer;
use Opulence\Ioc\Bootstrappers\Inspection\TargetedInspectionBinding;
use Opulence\Ioc\Bootstrappers\Inspection\UniversalInspectionBinding;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\Tests\Bootstrappers\Inspection\Mocks\Foo;
use Opulence\Ioc\Tests\Bootstrappers\Inspection\Mocks\IFoo;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\Bootstrapper;
use PHPUnit\Framework\TestCase;

/**
 * Tests the binding inspection container
 */
class BindingInspectionContainerTest extends TestCase
{
    /** @var BindingInspectionContainer */
    private $container;

    protected function setUp(): void
    {
        $this->container = new BindingInspectionContainer();
    }

    public function testBindingMethodsCreatesTargetedBindings(): void
    {
        $expectedBootstrapper = new class extends Bootstrapper {
            public function registerBindings(IContainer $container): void
            {
                $container->for('bar', function (IContainer $container) {
                    $container->bindFactory(IFoo::class, function () {
                        return new Foo();
                    });
                    $container->bindInstance(IFoo::class, new Foo());
                    $container->bindPrototype(IFoo::class, Foo::class);
                    $container->bindSingleton(IFoo::class, Foo::class);
                });
            }
        };
        $this->container->setBootstrapper($expectedBootstrapper);
        $expectedBootstrapper->registerBindings($this->container);
        $actualBindings = $this->container->getBindings();

        /** @var TargetedInspectionBinding $actualBinding */
        foreach ($actualBindings as $actualBinding) {
            $this->assertInstanceOf(TargetedInspectionBinding::class, $actualBinding);
            $this->assertEquals('bar', $actualBinding->getTargetClass());
            $this->assertEquals(IFoo::class, $actualBinding->getInterface());
            $this->assertSame($expectedBootstrapper, $actualBinding->getBootstrapper());
        }
    }

    public function testBindingMethodsCreatesUniversalBindings(): void
    {
        $expectedBootstrapper = new class extends Bootstrapper {
            public function registerBindings(IContainer $container): void
            {
                $container->bindFactory(IFoo::class, function () {
                    return new Foo();
                });
                $container->bindInstance(IFoo::class, new Foo());
                $container->bindPrototype(IFoo::class, Foo::class);
                $container->bindSingleton(IFoo::class, Foo::class);
            }
        };
        $this->container->setBootstrapper($expectedBootstrapper);
        $expectedBootstrapper->registerBindings($this->container);
        $actualBindings = $this->container->getBindings();

        foreach ($actualBindings as $actualBinding) {
            $this->assertInstanceOf(UniversalInspectionBinding::class, $actualBinding);
            $this->assertEquals(IFoo::class, $actualBinding->getInterface());
            $this->assertSame($expectedBootstrapper, $actualBinding->getBootstrapper());
        }
    }

    public function testCallingClosureReturnsNull(): void
    {
        $this->assertNull($this->container->callClosure(function () {
            return 'foo';
        }));
    }

    public function testCallingMethodReturnsNull(): void
    {
        $object = new class {
            public function foo(): string
            {
                return 'foo';
            }
        };
        $this->assertNull($this->container->callMethod($object, 'foo'));
    }

    public function testHasBindingReturnsNull(): void
    {
        $this->assertFalse($this->container->hasBinding('foo'));
    }

    public function testResolveReturnsNull(): void
    {
        $this->assertNull($this->container->resolve('foo'));
    }

    public function testTryResolveReturnsFalse(): void
    {
        $instance = null;
        $this->assertFalse($this->container->tryResolve('foo', $instance));
    }
}
