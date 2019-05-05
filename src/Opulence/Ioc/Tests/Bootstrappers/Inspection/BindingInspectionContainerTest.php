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

    public function testBindingSameTargetedBindingTwiceOnlyRegistersItOnce(): void
    {
        $bootstrapper = new class extends Bootstrapper {
            public function registerBindings(IContainer $container): void
            {
                $container->for('foo', function (IContainer $container) {
                    $container->bindSingleton(IFoo::class, Foo::class);
                });
            }
        };
        $this->container->setBootstrapper($bootstrapper);
        $bootstrapper->registerBindings($this->container);
        // Re-register bindings
        $bootstrapper->registerBindings($this->container);
        /** @var TargetedInspectionBinding[] $actualBindings */
        $actualBindings = $this->container->getBindings();
        $this->assertCount(1, $actualBindings);
        $this->assertEquals('foo', $actualBindings[0]->getTargetClass());
        $this->assertEquals(IFoo::class, $actualBindings[0]->getInterface());
        $this->assertSame($bootstrapper, $actualBindings[0]->getBootstrapper());
    }

    public function testBindingSameUniversalBindingTwiceOnlyRegistersItOnce(): void
    {
        $bootstrapper = new class extends Bootstrapper {
            public function registerBindings(IContainer $container): void
            {
                $container->bindSingleton(IFoo::class, Foo::class);
            }
        };
        $this->container->setBootstrapper($bootstrapper);
        $bootstrapper->registerBindings($this->container);
        // Re-register bindings
        $bootstrapper->registerBindings($this->container);
        $actualBindings = $this->container->getBindings();
        $this->assertCount(1, $actualBindings);
        $this->assertEquals(IFoo::class, $actualBindings[0]->getInterface());
        $this->assertSame($bootstrapper, $actualBindings[0]->getBootstrapper());
    }
}
