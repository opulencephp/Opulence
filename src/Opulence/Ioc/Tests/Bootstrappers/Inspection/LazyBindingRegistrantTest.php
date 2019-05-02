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

use Closure;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\Inspection\LazyBindingRegistrant;
use Opulence\Ioc\Bootstrappers\Inspection\TargetedInspectionBinding;
use Opulence\Ioc\Bootstrappers\Inspection\UniversalInspectionBinding;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\Tests\Bootstrappers\Inspection\Mocks\Foo;
use Opulence\Ioc\Tests\Bootstrappers\Inspection\Mocks\IFoo;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the lazy binding registrant
 */
class LazyBindingRegistrantTest extends TestCase
{
    /** @var LazyBindingRegistrant */
    private $registrant;
    /** @var IContainer|MockObject */
    private $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(IContainer::class);
        $this->registrant = new LazyBindingRegistrant($this->container);
    }

    public function testRegisteringTargetedBindingAndResolvingItWillAddBindingSetInConstructor(): void
    {
        $bootstrapper = new class extends Bootstrapper {
            /** @var IFoo Used so we can verify what gets bound */
            public $foo;

            public function registerBindings(IContainer $container): void
            {
                $this->foo = new Foo();
                $container->for('bar', function (IContainer $container) {
                    $container->bindInstance(IFoo::class, $this->foo);
                });
            }
        };
        $bindings = [new TargetedInspectionBinding('bar', IFoo::class, $bootstrapper)];
        $initialCallback = function (IContainer $container) {
            // Don't do anything
        };
        /**
         * NOTE: We won't actually call the for() callbacks until the very end of this test.
         * So, any calls to the container within those callbacks will be executed last, hence the order of expectations.
         */
        // For binding the initial factory
        $this->container->expects($this->at(0))
            ->method('for')
            ->with('bar', $this->callback(function (Closure $callback) use (&$initialCallback) {
                $initialCallback = $callback;

                return true;
            }));
        $initialFactory = function () {
            // Don't do anything
        };
        // Binding the actual factory
        $this->container->expects($this->at(1))
            ->method('bindFactory')
            ->with(IFoo::class, $this->callback(function (Closure $factory) use (&$initialFactory) {
                $initialFactory = $factory;

                return true;
            }));
        $unbindCallback = function (IContainer $container) {
            // Don't do anything
        };
        // For unbinding the initial factory
        $this->container->expects($this->at(2))
            ->method('for')
            ->with('bar', $this->callback(function (Closure $callback) use (&$unbindCallback) {
                $unbindCallback = $callback;

                return true;
            }));
        $bootstrapperCallback = function (IContainer $container) {
            // Don't do anything
        };
        // For binding the instance from within the bootstrapper
        $this->container->expects($this->at(3))
            ->method('for')
            ->with('bar', $this->callback(function (Closure $callback) use (&$bootstrapperCallback) {
                $bootstrapperCallback = $callback;

                return true;
            }));
        $resolutionCallback = function (IContainer $container) {
            // Don't do anything
        };
        // For resolving the interface
        $this->container->expects($this->at(4))
            ->method('for')
            ->with('bar', $this->callback(function (Closure $callback) use (&$resolutionCallback) {
                $resolutionCallback = $callback;

                return true;
            }));
        // Unbind the initial factory
        $this->container->expects($this->at(5))
            ->method('unbind')
            ->with(IFoo::class);
        // Bind the instance from within the bootstrapper
        $this->container->expects($this->at(6))
            ->method('bindInstance')
            ->with(IFoo::class, $this->callback(function (Foo $foo) {
                return true;
            }));
        // Resolve the interface
        $this->container->method('resolve')
            ->with(IFoo::class)
            ->willReturn($bootstrapper->foo);
        $this->registrant->registerBindings($bindings);
        $initialCallback($this->container);
        $initialFactory();
        $unbindCallback($this->container);
        $bootstrapperCallback($this->container);
        $resolutionCallback($this->container);
    }

    public function testRegisteringTargetedBindingRegistersBindingsFromBootstrapper(): void
    {
        $bootstrapper = new class extends Bootstrapper {
            public function registerBindings(IContainer $container): void
            {
                $container->for('bar', function (IContainer $container) {
                    $container->bindInstance(IFoo::class, new Foo());
                });
            }
        };
        $bindings = [new TargetedInspectionBinding('bar', IFoo::class, $bootstrapper)];
        $this->container->expects($this->once())
            ->method('for')
            ->with('bar', $this->callback(function (Closure $factory) {
                return $factory instanceof Closure;
            }));
        $this->registrant->registerBindings($bindings);
    }

    public function testRegisteringUniversalBindingAndResolvingItWillAddBindingSetInConstructor(): void
    {
        $bootstrapper = new class extends Bootstrapper {
            public function registerBindings(IContainer $container): void
            {
                $container->bindInstance(IFoo::class, new Foo());
            }
        };
        $bindings = [new UniversalInspectionBinding(IFoo::class, $bootstrapper)];
        $actualFactory = function () {
            // Don't do anything
        };
        $this->container->expects($this->at(0))
            ->method('bindFactory')
            ->with(IFoo::class, $this->callback(function (Closure $factory) use (&$actualFactory) {
                $actualFactory = $factory;

                return $factory instanceof Closure;
            }));
        $this->container->expects($this->at(1))
            ->method('unbind')
            ->with(IFoo::class);
        $this->container->expects($this->at(2))
            ->method('bindInstance')
            ->with(IFoo::class, $this->callback(function (Foo $foo) {
                return $foo instanceof Foo;
            }));
        $this->registrant->registerBindings($bindings);
        $actualFactory();
    }

    public function testRegisteringUniversalBindingRegistersBindingsFromBootstrapper(): void
    {
        $bootstrapper = new class extends Bootstrapper {
            public function registerBindings(IContainer $container): void
            {
                $container->bindInstance(IFoo::class, new Foo());
            }
        };
        $bindings = [new UniversalInspectionBinding(IFoo::class, $bootstrapper)];
        $this->container->expects($this->once())
            ->method('bindFactory')
            ->with(IFoo::class, $this->callback(function (Closure $factory) {
                return $factory instanceof Closure;
            }));
        $this->registrant->registerBindings($bindings);
    }
}
