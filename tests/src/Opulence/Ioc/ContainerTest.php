<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc;

use InvalidArgumentException;
use Opulence\Tests\Ioc\Mocks\Bar;
use Opulence\Tests\Ioc\Mocks\BaseClass;
use Opulence\Tests\Ioc\Mocks\Blah;
use Opulence\Tests\Ioc\Mocks\ConstructorWithConcreteClass;
use Opulence\Tests\Ioc\Mocks\ConstructorWithDefaultValuePrimitives;
use Opulence\Tests\Ioc\Mocks\ConstructorWithInterface;
use Opulence\Tests\Ioc\Mocks\ConstructorWithMixOfConcreteClassesAndPrimitives;
use Opulence\Tests\Ioc\Mocks\ConstructorWithMixOfInterfacesAndPrimitives;
use Opulence\Tests\Ioc\Mocks\ConstructorWithPrimitives;
use Opulence\Tests\Ioc\Mocks\ConstructorWithSetters;
use Opulence\Tests\Ioc\Mocks\Dave;
use Opulence\Tests\Ioc\Mocks\Foo;
use Opulence\Tests\Ioc\Mocks\IFoo;
use Opulence\Tests\Ioc\Mocks\IPerson;
use Opulence\Tests\Ioc\Mocks\MagicCallMethod;
use Opulence\Tests\Ioc\Mocks\StaticSetters;
use stdClass;

/**
 * Tests the dependency injection container
 */
class ContainerTest extends \PHPUnit\Framework\TestCase
{
    /** @var Container The container to use in tests */
    private $container = null;
    /** @var string The name of the simple interface to use in tests */
    private $fooInterface = IFoo::class;
    /** @var string The name of the simple interface to use in tests */
    private $personInterface = IPerson::class;
    /** @var string The name of a class that implements IPerson */
    private $concretePerson = Dave::class;
    /** @var string The name of the base class to use in tests */
    private $baseClass = BaseClass::class;
    /** @var string The name of the class that implements IFoo to use in tests */
    private $concreteFoo = Bar::class;
    /** @var string The name of a second class that implements the IFoo to use in tests */
    private $secondConcreteIFoo = Blah::class;
    /** @var string The name of a another class that implements the IFoo to use in tests */
    private $concreteFooWithIPersonDependency = Foo::class;
    /** @var string The name of the class that accepts the IFoo in its constructor */
    private $constructorWithIFoo = ConstructorWithInterface::class;
    /** @var string The name of the class that accepts the concrete class in its constructor */
    private $constructorWithConcreteClass = ConstructorWithConcreteClass::class;
    /** @var string The name of the class that accepts a mix of interfaces and primitives in its constructor */
    private $constructorWithInterfacesAndPrimitives = ConstructorWithMixOfInterfacesAndPrimitives::class;
    /** @var string The name of the class that accepts a mix of class names and primitives in its constructor */
    private $constructorWithConcreteClassesAndPrimitives = ConstructorWithMixOfConcreteClassesAndPrimitives::class;
    /** @var string The name of the class that accepts the primitives in its constructor */
    private $constructorWithPrimitives = ConstructorWithPrimitives::class;
    /** @var string The name of the class that accepts primitives with default values in its constructor */
    private $constructorWithDefaultValuePrimitives = ConstructorWithDefaultValuePrimitives::class;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->container = new Container();
    }

    /**
     * Tests binding a targeted factory
     */
    public function testBindingTargetedFactory()
    {
        $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            $container->bindFactory($this->fooInterface, function () {
                return new $this->concreteFoo;
            });
        });
        $instance1 = $this->container->resolve($this->constructorWithIFoo);
        $instance2 = $this->container->resolve($this->constructorWithIFoo);
        $this->assertInstanceOf($this->concreteFoo, $instance1->getFoo());
        $this->assertInstanceOf($this->concreteFoo, $instance2->getFoo());
        $this->assertNotSame($instance1->getFoo(), $instance2->getFoo());
        $this->assertNotSame($instance1, $instance2);
    }

    /**
     * Tests binding a targeted singleton factory
     */
    public function testBindingTargetedSingletonFactory()
    {
        $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            $container->bindFactory($this->fooInterface, function () {
                return new $this->concreteFoo;
            }, true);
        });
        $instance1 = $this->container->resolve($this->constructorWithIFoo);
        $instance2 = $this->container->resolve($this->constructorWithIFoo);
        $this->assertInstanceOf($this->constructorWithIFoo, $instance1);
        $this->assertInstanceOf($this->concreteFoo, $instance1->getFoo());
        $this->assertSame($instance1->getFoo(), $instance2->getFoo());
        $this->assertNotSame($instance1, $instance2);
        $this->assertNotSame($instance1, $instance2);
    }

    /**
     * Tests binding to an abstract class
     */
    public function testBindingToAbstractClass()
    {
        $prototypeContainer = new Container();
        $prototypeContainer->bindPrototype($this->baseClass, $this->concreteFoo);
        $prototypeInstance = $prototypeContainer->resolve($this->baseClass);
        $this->assertInstanceOf($this->concreteFoo, $prototypeInstance);
        $this->assertNotSame($prototypeInstance, $prototypeContainer->resolve($this->baseClass));
        $singletonContainer = new Container();
        $singletonContainer->bindSingleton($this->baseClass, $this->concreteFoo);
        $singletonInstance = $singletonContainer->resolve($this->baseClass);
        $this->assertInstanceOf($this->concreteFoo, $singletonInstance);
        $this->assertSame($singletonInstance, $singletonContainer->resolve($this->baseClass));
    }

    /**
     * Tests binding a universal factory
     */
    public function testBindingUniversalFactory()
    {
        $this->container->bindFactory($this->fooInterface, function () {
            return new $this->concreteFoo;
        });
        $instance1 = $this->container->resolve($this->fooInterface);
        $instance2 = $this->container->resolve($this->fooInterface);
        $this->assertInstanceOf($this->concreteFoo, $instance1);
        $this->assertNotSame($instance1, $instance2);
    }

    /**
     * Tests binding a universal singleton factory
     */
    public function testBindingUniversalSingletonFactory()
    {
        $this->container->bindFactory($this->fooInterface, function () {
            return new $this->concreteFoo;
        }, true);
        $instance1 = $this->container->resolve($this->constructorWithIFoo);
        $instance2 = $this->container->resolve($this->constructorWithIFoo);
        $this->assertInstanceOf($this->constructorWithIFoo, $instance1);
        $this->assertInstanceOf($this->concreteFoo, $instance1->getFoo());
        $this->assertInstanceOf($this->concreteFoo, $instance2->getFoo());
        $this->assertSame($instance1->getFoo(), $instance2->getFoo());
        $this->assertNotSame($instance1, $instance2);
    }

    /**
     * Tests calling a method with primitive types
     */
    public function testCallingMethodWithPrimitiveTypes()
    {
        $instance = new ConstructorWithSetters();
        $this->container->callMethod($instance, 'setPrimitive', ['foo']);
        $this->assertSame('foo', $instance->getPrimitive());
        $result = $this->container->callClosure(
            function ($primitive) {
                return $primitive;
            },
            ['foo']
        );
        $this->assertEquals('foo', $result);
    }

    /**
     * Tests calling a method with primitive types without specifying a value
     */
    public function testCallingMethodWithPrimitiveTypesWithoutSpecifyingValue()
    {
        $this->expectException(IocException::class);
        $instance = new ConstructorWithSetters();
        $this->container->callMethod($instance, 'setPrimitive');
    }

    /**
     * Tests calling a method with type-hinted and primitive types
     */
    public function testCallingMethodWithTypeHintedAndPrimitiveTypes()
    {
        $this->container->bindSingleton($this->fooInterface, $this->concreteFoo);
        $instance = new ConstructorWithSetters();
        $this->container->callMethod($instance, 'setBoth', ['foo']);
        $this->assertInstanceOf($this->concreteFoo, $instance->getInterface());
        $this->assertSame('foo', $instance->getPrimitive());
        $response = $this->container->callClosure(
            function (IFoo $interface, $primitive) {
                return get_class($interface) . ':' . $primitive;
            },
            ['foo']
        );
        $this->assertEquals($this->concreteFoo . ':foo', $response);
    }

    /**
     * Tests calling a method with type hints
     */
    public function testCallingMethodWithTypeHints()
    {
        $this->container->bindSingleton($this->fooInterface, $this->concreteFoo);
        $instance = new ConstructorWithSetters();
        $this->container->callMethod($instance, 'setInterface');
        $this->assertInstanceOf($this->concreteFoo, $instance->getInterface());
        $response = $this->container->callClosure(
            function (IFoo $interface) {
                return get_class($interface);
            }
        );
        $this->assertEquals($this->concreteFoo, $response);
    }

    /**
     * Tests calling a non-existent method
     */
    public function testCallingNonExistentMethod()
    {
        $this->expectException(IocException::class);
        $instance = new ConstructorWithSetters();
        $this->container->callMethod($instance, 'foobar');
    }

    /**
     * Tests calling a non-existent method and ignoring that it is missing
     */
    public function testCallingNonExistentMethodAndIgnoringThatItIsMissing()
    {
        $instance = new ConstructorWithSetters();
        $this->assertNull($this->container->callMethod($instance, 'foobar', [], true));
    }

    /**
     * Tests calling a non-existent method on a class that has a magic call method
     */
    public function testCallingNonExistentMethodOnClassThatHasMagicCallMethod()
    {
        $instance = new MagicCallMethod();
        $this->assertNull($this->container->callMethod($instance, 'foobar', [], true));
    }

    /**
     * Tests calling a static method
     */
    public function testCallingStaticMethod()
    {
        $person = new $this->concretePerson;
        $this->container->bindInstance($this->personInterface, $person);
        $this->container->callMethod(StaticSetters::class, 'setStaticSetterDependency', [$person]);
        $this->assertSame($person, StaticSetters::$staticDependency);
    }

    /**
     * Tests if a target-bound interface is bound
     */
    public function testCheckingIfTargetBoundInterfaceIsBound()
    {
        $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            $container->bindPrototype($this->fooInterface, $this->concreteFoo);
        });
        $this->assertTrue($this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            return $container->hasBinding($this->fooInterface);
        }));
        // Reset for factory
        $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            $container->unbind($this->fooInterface);
        });
        $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            $container->bindFactory($this->fooInterface, function () {
                return new $this->concreteFoo;
            });
        });
        $this->assertTrue($this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            return $container->hasBinding($this->fooInterface);
        }));
        // Reset for instance
        $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            $container->unbind($this->fooInterface);
        });
        $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            $container->bindInstance($this->fooInterface, new $this->concreteFoo);
        });
        $this->assertTrue($this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            return $container->hasBinding($this->fooInterface);
        }));
        // Reset for singleton
        $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            $container->unbind($this->fooInterface);
        });
        $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            $container->bindSingleton($this->fooInterface, $this->concreteFoo);
        });
        $this->assertTrue($this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            return $container->hasBinding($this->fooInterface);
        }));
    }

    /**
     * Tests if a universally bound interface is bound
     */
    public function testCheckingIfUniversallyBoundInterfaceIsBound()
    {
        $this->container->bindPrototype($this->fooInterface, $this->concreteFoo);
        $this->assertTrue($this->container->hasBinding($this->fooInterface));
        $this->container->unbind($this->fooInterface);
        $this->container->bindFactory($this->fooInterface, function () {
            return new $this->concreteFoo;
        });
        $this->assertTrue($this->container->hasBinding($this->fooInterface));
    }

    /**
     * Tests that a target has a binding when it only has a universal binding
     */
    public function testCheckingTargetHasBindingWhenItOnlyHasUniversalBinding()
    {
        $this->container->bindPrototype($this->fooInterface, $this->concreteFoo);
        $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            $this->assertTrue($container->hasBinding($this->fooInterface));
        });
    }

    /**
     * Tests checking an unbound targeted binding
     */
    public function testCheckingUnboundTargetedBinding()
    {
        $this->assertFalse(
            $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
                return $container->hasBinding($this->fooInterface);
            })
        );
    }

    /**
     * Tests checking an unbound universal binding
     */
    public function testCheckingUnboundUniversalBinding()
    {
        $this->assertFalse($this->container->hasBinding($this->fooInterface));
    }

    /**
     * Tests getting a universal binding
     */
    public function testCheckingUniversalBinding()
    {
        $this->container->bindSingleton($this->fooInterface, $this->concreteFoo);
        $this->assertTrue($this->container->hasBinding($this->fooInterface));
    }

    /**
     * Tests creating a shared instance object with an unset constructor primitive
     */
    public function testCreatingInstanceWithUnsetConstructorPrimitive()
    {
        $this->expectException(IocException::class);
        $this->container->resolve($this->constructorWithPrimitives);
    }

    /**
     * Tests creating an interface without binding a concrete implementation
     */
    public function testCreatingInterfaceWithoutBinding()
    {
        $this->expectException(IocException::class);
        $this->container->resolve($this->fooInterface);
    }

    /**
     * Tests creating a prototype instance with a concrete dependency
     */
    public function testCreatingPrototypeInstanceWithConcreteDependency()
    {
        $newInstance = $this->container->resolve($this->constructorWithConcreteClass);
        $this->assertInstanceOf($this->constructorWithConcreteClass, $newInstance);
    }

    /**
     * Tests creating a prototype object with a constructor primitive
     */
    public function testCreatingPrototypeObjectWithConstructorPrimitive()
    {
        $this->container->bindPrototype($this->constructorWithPrimitives, null, ['foo', 'bar']);
        $instance = $this->container->resolve($this->constructorWithPrimitives);
        $this->assertInstanceOf($this->constructorWithPrimitives, $instance);
        $this->assertNotSame($instance,
            $this->container->resolve($this->constructorWithPrimitives));
    }

    /**
     * Tests creating a prototype object with an unset constructor primitive
     */
    public function testCreatingPrototypeObjectWithUnsetConstructorPrimitive()
    {
        $this->expectException(IocException::class);
        $this->container->resolve($this->constructorWithPrimitives);
    }

    /**
     * Tests creating a prototype object with an unset constructor primitive with a default value
     */
    public function testCreatingPrototypeObjectWithUnsetConstructorPrimitiveWithDefaultValue()
    {
        $this->container->bindPrototype($this->constructorWithDefaultValuePrimitives, null, ['foo']);
        $instance = $this->container->resolve($this->constructorWithDefaultValuePrimitives);
        $this->assertInstanceOf($this->constructorWithDefaultValuePrimitives, $instance);
        $this->assertNotSame(
            $instance,
            $this->container->resolve($this->constructorWithDefaultValuePrimitives)
        );
    }

    /**
     * Tests creating a singleton instance with a concrete dependency
     */
    public function testCreatingSingletonInstanceWithConcreteDependency()
    {
        $sharedInstance = $this->container->resolve($this->constructorWithConcreteClass);
        $this->assertInstanceOf($this->constructorWithConcreteClass, $sharedInstance);
    }

    /**
     * Tests creating a singleton instance with a constructor primitive
     */
    public function testCreatingSingletonInstanceWithConstructorPrimitive()
    {
        $this->container->bindSingleton($this->constructorWithPrimitives, null, ['foo', 'bar']);
        $instance = $this->container->resolve($this->constructorWithPrimitives);
        $this->assertInstanceOf($this->constructorWithPrimitives, $instance);
        $this->assertSame(
            $instance,
            $this->container->resolve($this->constructorWithPrimitives)
        );
    }

    /**
     * Tests creating a singleton instance with an unset constructor primitive with a default value
     */
    public function testCreatingSingletonInstanceWithUnsetConstructorPrimitiveWithDefaultValue()
    {
        $this->container->bindSingleton($this->constructorWithDefaultValuePrimitives, null, ['foo']);
        $instance = $this->container->resolve($this->constructorWithDefaultValuePrimitives);
        $this->assertInstanceOf($this->constructorWithDefaultValuePrimitives, $instance);
        $this->assertSame(
            $instance,
            $this->container->resolve($this->constructorWithDefaultValuePrimitives)
        );
    }

    /**
     * Tests creating a class that has a dependency that has a dependency
     */
    public function testDependencyThatHasDependency()
    {
        $tests = function () {
            $this->assertInstanceOf(
                $this->concreteFooWithIPersonDependency,
                $this->container->resolve($this->fooInterface)
            );
        };
        $this->container->bindPrototype($this->fooInterface, $this->concreteFooWithIPersonDependency);
        $this->container->bindPrototype($this->personInterface, $this->concretePerson);
        $tests();
        $this->container->unbind([$this->fooInterface, $this->personInterface]);
        $this->container->bindFactory($this->fooInterface, function () {
            return new $this->concreteFooWithIPersonDependency(new $this->concretePerson);
        });
        $this->container->bindFactory($this->personInterface, function () {
            return new $this->concretePerson;
        });
        $tests();
    }

    /**
     * Tests creating a class that has a dependency that has a dependency without binding all dependencies
     */
    public function testDependencyThatHasDependencyWithoutBindingAllDependencies()
    {
        $this->expectException(IocException::class);
        $this->container->bindSingleton($this->fooInterface, $this->concreteFooWithIPersonDependency);
        $this->container->resolve($this->fooInterface);
    }

    /**
     * Tests factory dependencies in prototype are not the same
     */
    public function testFactoryDependenciesInPrototypeAreNotSame()
    {
        $this->container->bindPrototype($this->constructorWithInterfacesAndPrimitives, null, [23]);
        $this->container->bindFactory($this->fooInterface, function () {
            return new $this->concreteFoo;
        });
        $this->container->bindFactory($this->personInterface, function () {
            return new $this->concretePerson;
        });
        /** @var ConstructorWithMixOfInterfacesAndPrimitives $instance1 */
        $instance1 = $this->container->resolve($this->constructorWithInterfacesAndPrimitives);
        /** @var ConstructorWithMixOfInterfacesAndPrimitives $instance2 */
        $instance2 = $this->container->resolve($this->constructorWithInterfacesAndPrimitives);
        $this->assertNotSame($instance1->getFoo(), $instance2->getFoo());
        $this->assertNotSame($instance1->getPerson(), $instance2->getPerson());
    }

    /**
     * Tests factory dependencies in singletons
     */
    public function testFactoryDependenciesInSingleton()
    {
        $this->container->bindSingleton($this->constructorWithInterfacesAndPrimitives, null, [23]);
        $this->container->bindFactory($this->fooInterface, function () {
            return new $this->concreteFoo;
        });
        $this->container->bindFactory($this->personInterface, function () {
            return new $this->concretePerson;
        });
        /** @var ConstructorWithMixOfInterfacesAndPrimitives $instance1 */
        $instance1 = $this->container->resolve($this->constructorWithInterfacesAndPrimitives);
        /** @var ConstructorWithMixOfInterfacesAndPrimitives $instance2 */
        $instance2 = $this->container->resolve($this->constructorWithInterfacesAndPrimitives);
        $this->assertInstanceOf($this->constructorWithInterfacesAndPrimitives, $instance1);
        $this->assertSame($instance1, $instance2);
        $this->assertEquals(23, $instance1->getId());
        $this->assertEquals(23, $instance2->getId());
    }

    /**
     * Tests getting a targeted binding when no targeted binding exists but a universal one does
     */
    public function testGettingTargetedBindingWhenOneDoesNotExistButUniversalBindingExists()
    {
        $this->container->bindSingleton($this->fooInterface, $this->concreteFoo);
        $instance = $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            return $container->resolve($this->fooInterface);
        });
        $this->assertInstanceOf($this->fooInterface, $instance);
    }

    /**
     * Tests that instances are different when using factory
     */
    public function testInstancesAreDifferentWhenUsingFactory()
    {
        $this->container->bindFactory($this->baseClass, function () {
            return new $this->concreteFoo;
        });
        $instance1 = $this->container->resolve($this->baseClass);
        $instance2 = $this->container->resolve($this->baseClass);
        $this->assertNotSame($instance1, $instance2);
    }

    /**
     * Tests that an exception is thrown when binding a factory to an invalid interface type
     */
    public function testInvalidArgumentTypeWhenBindingFactoryThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->container->bindFactory(new stdClass(), function () {
        });
    }

    /**
     * Tests that an exception is thrown when binding an instance to an invalid interface type
     */
    public function testInvalidArgumentTypeWhenBindingInstanceThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->container->bindInstance(new stdClass(), new stdClass());
    }

    /**
     * Tests that an exception is thrown when binding a prototype to an invalid interface type
     */
    public function testInvalidArgumentTypeWhenBindingPrototypeThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->container->bindPrototype(new stdClass(), Foo::class);
    }

    /**
     * Tests that an exception is thrown when binding a singleton to an invalid interface type
     */
    public function testInvalidArgumentTypeWhenBindingSingletonThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->container->bindSingleton(new stdClass(), Foo::class);
    }

    /**
     * Tests instantiating a class with a mix of concrete classes and primitives in its constructor for a prototype
     */
    public function testMixOfConcreteClassesAndPrimitivesInConstructorForPrototype()
    {
        /** @var ConstructorWithMixOfConcreteClassesAndPrimitives $sharedInstance */
        $this->container->bindPrototype($this->constructorWithConcreteClassesAndPrimitives, null, [23]);
        /** @var ConstructorWithMixOfConcreteClassesAndPrimitives $instance */
        $instance = $this->container->resolve($this->constructorWithConcreteClassesAndPrimitives);
        $this->assertInstanceOf($this->constructorWithConcreteClassesAndPrimitives, $instance);
        $this->assertEquals(23, $instance->getId());
        $this->assertNotSame($instance, $this->container->resolve($this->constructorWithConcreteClassesAndPrimitives));
    }

    /**
     * Tests instantiating a class with a mix of concrete classes and primitives in its constructor for a singleton
     */
    public function testMixOfConcreteClassesAndPrimitivesInConstructorForSingleton()
    {
        /** @var ConstructorWithMixOfConcreteClassesAndPrimitives $instance */
        $this->container->bindSingleton($this->constructorWithConcreteClassesAndPrimitives, null, [23]);
        $instance = $this->container->resolve($this->constructorWithConcreteClassesAndPrimitives);
        /** @var ConstructorWithMixOfConcreteClassesAndPrimitives $newInstance */
        $this->assertInstanceOf($this->constructorWithConcreteClassesAndPrimitives, $instance);
        $this->assertEquals(23, $instance->getId());
        $this->assertSame($instance, $this->container->resolve($this->constructorWithConcreteClassesAndPrimitives));
    }

    /**
     * Tests registering multiple targeted bindings
     */
    public function testMultipleTargetedBindings()
    {
        $this->container->for('baz', function (IContainer $container) {
            $container->bindSingleton(['foo', 'bar'], $this->concreteFoo);
        });
        $this->assertTrue($this->container->for('baz', function (IContainer $container) {
            return $container->hasBinding('foo');
        }));
        $this->assertTrue($this->container->for('baz', function (IContainer $container) {
            return $container->hasBinding('bar');
        }));
    }

    /**
     * Tests registering multiple universal bindings
     */
    public function testMultipleUniversalBindings()
    {
        $this->container->bindSingleton(['foo', 'bar'], $this->concreteFoo);
        $this->assertTrue($this->container->hasBinding('foo'));
        $this->assertTrue($this->container->hasBinding('bar'));
    }

    /**
     * Tests resolving an instance bound in a targeted callback
     */
    public function testResolvingInstanceBoundInTargetedCallback()
    {
        $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            $container->bindFactory($this->fooInterface, function () {
                return new $this->concreteFoo;
            });
        });
        $instance = $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            return $container->resolve($this->fooInterface);
        });
        $this->assertInstanceOf($this->concreteFoo, $instance);
    }

    /**
     * Tests making a prototype for target
     */
    public function testResolvingPrototypeForTarget()
    {
        $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            $container->bindPrototype($this->fooInterface, $this->concreteFoo);
        });
        $instance1 = $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            return $container->resolve($this->fooInterface);
        });
        $instance2 = $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            return $container->resolve($this->fooInterface);
        });
        $this->assertInstanceOf($this->concreteFoo, $instance1);
        $this->assertInstanceOf($this->concreteFoo, $instance2);
        $this->assertNotSame($instance1, $instance2);
    }

    /**
     * Tests making a prototype non-existent class
     */
    public function testResolvingPrototypeNonExistentClass()
    {
        $this->expectException(IocException::class);
        $this->container->resolve('DoesNotExist');
    }

    /**
     * Tests making singleton for target
     */
    public function testResolvingSingletonForTarget()
    {
        $this->container->for('foo', function (IContainer $container) {
            $container->bindSingleton($this->fooInterface, $this->concreteFoo);
        });
        $instance1 = $this->container->for('foo', function (IContainer $container) {
            return $container->resolve($this->fooInterface);
        });
        $instance2 = $this->container->for('foo', function (IContainer $container) {
            return $container->resolve($this->fooInterface);
        });
        $this->assertInstanceOf($this->concreteFoo, $instance1);
        $this->assertInstanceOf($this->concreteFoo, $instance2);
        $this->assertSame($instance1, $instance2);

        // Make sure that the singleton is not bound universally
        try {
            $this->container->resolve($this->constructorWithIFoo);
            // The line above should throw an exception, so fail if we've gotten here
            $this->fail('Targeted singleton accidentally bound universally');
        } catch (IocException $ex) {
            // Don't do anything
        }
    }

    /**
     * Tests singleton dependencies in prototype
     */
    public function testSingletonDependenciesInPrototype()
    {
        $this->container->bindPrototype($this->constructorWithInterfacesAndPrimitives, null, [23]);
        $this->container->bindSingleton($this->fooInterface, $this->concreteFoo);
        $this->container->bindSingleton($this->personInterface, $this->concretePerson);
        /** @var ConstructorWithMixOfInterfacesAndPrimitives $instance1 */
        $instance1 = $this->container->resolve($this->constructorWithInterfacesAndPrimitives);
        /** @var ConstructorWithMixOfInterfacesAndPrimitives $instance2 */
        $instance2 = $this->container->resolve($this->constructorWithInterfacesAndPrimitives);
        $this->assertSame($instance1->getFoo(), $instance2->getFoo());
        $this->assertSame($instance1->getPerson(), $instance2->getPerson());
        $this->assertNotSame($instance1, $instance2);
    }

    /**
     * Tests singleton dependencies in singletons
     */
    public function testSingletonDependenciesInSingleton()
    {
        $this->container->bindSingleton($this->constructorWithInterfacesAndPrimitives, null, [23]);
        $this->container->bindSingleton($this->fooInterface, $this->concreteFoo);
        $this->container->bindSingleton($this->personInterface, $this->concretePerson);
        /** @var ConstructorWithMixOfInterfacesAndPrimitives $instance1 */
        $instance1 = $this->container->resolve($this->constructorWithInterfacesAndPrimitives);
        /** @var ConstructorWithMixOfInterfacesAndPrimitives $instance2 */
        $instance2 = $this->container->resolve($this->constructorWithInterfacesAndPrimitives);
        $this->assertInstanceOf($this->constructorWithInterfacesAndPrimitives, $instance1);
        $this->assertSame($instance1, $instance2);
        $this->assertEquals(23, $instance1->getId());
        $this->assertEquals(23, $instance2->getId());
        $this->assertSame($instance1->getPerson(), $instance2->getPerson());
    }

    /**
     * Tests a targeted binding of an instance to an interface
     */
    public function testTargetedBindingOfInstanceToInterface()
    {
        $targetedInstance = new $this->concreteFoo();
        $this->container->for($this->constructorWithIFoo, function (IContainer $container) use ($targetedInstance) {
            $container->bindInstance($this->fooInterface, $targetedInstance);
        });
        // This universal binding should NOT take precedence over the class binding
        $this->container->bindPrototype($this->fooInterface, $this->secondConcreteIFoo);
        $resolvedInstance = $this->container->resolve($this->constructorWithIFoo);
        $this->assertSame($targetedInstance, $resolvedInstance->getFoo());
    }

    /**
     * Tests that targeted factory bindings only apply to the next call
     */
    public function testTargetedFactoryBindingsOnlyApplyToNextCall()
    {
        $this->container->for('foo', function (IContainer $container) {
            $container->bindFactory($this->fooInterface, function () {
                return new $this->concreteFoo();
            });
        });
        $this->container->for('bar', function (IContainer $container) {
            $container->bindFactory('doesNotExist', function () {
                return new $this->concreteFoo();
            });
        });
        $this->assertFalse($this->container->for('foo', function (IContainer $container) {
            return $container->hasBinding('doesNotExist');
        }));
        $this->assertTrue($this->container->for('bar', function (IContainer $container) {
            return $container->hasBinding('doesNotExist');
        }));
    }

    /**
     * Tests that targeted instance bindings only apply to the next call
     */
    public function testTargetedInstanceBindingsOnlyApplyToNextCall()
    {
        $instance1 = new $this->concreteFoo();
        $instance2 = new $this->concreteFoo();
        $this->container->for('foo', function (IContainer $container) use ($instance1) {
            $container->bindInstance($this->fooInterface, $instance1);
        });
        $this->container->for('bar', function (IContainer $container) use ($instance2) {
            $container->bindInstance('doesNotExist', $instance2);
        });
        $this->assertFalse($this->container->for('foo', function (IContainer $container) {
            return $container->hasBinding('doesNotExist');
        }));
        $this->assertTrue($this->container->for('bar', function (IContainer $container) {
            return $container->hasBinding('doesNotExist');
        }));
    }

    /**
     * Tests that targeted prototype bindings only apply to the next call
     */
    public function testTargetedPrototypeBindingsOnlyApplyToNextCall()
    {
        $this->container->for('foo', function (IContainer $container) {
            $container->bindPrototype($this->fooInterface, 'bar');
        });
        $this->container->for('baz', function (IContainer $container) {
            $container->bindPrototype('doesNotExist', 'bar');
        });
        $this->assertFalse($this->container->for('foo', function (IContainer $container) {
            return $container->hasBinding('doesNotExist');
        }));
        $this->assertTrue($this->container->for('baz', function (IContainer $container) {
            return $container->hasBinding('doesNotExist');
        }));
    }

    /**
     * Tests that targeted singleton bindings only apply to the next call
     */
    public function testTargetedSingletonBindingsOnlyApplyToNextCall()
    {
        $this->container->for('foo', function (IContainer $container) {
            $container->bindSingleton($this->fooInterface, 'bar');
        });
        $this->container->for('baz', function (IContainer $container) {
            $container->bindSingleton('doesNotExist', 'bar');
        });
        $this->assertFalse($this->container->for('foo', function (IContainer $container) {
            return $container->hasBinding('doesNotExist');
        }));
        $this->assertTrue($this->container->for('baz', function (IContainer $container) {
            return $container->hasBinding('doesNotExist');
        }));
    }

    /**
     * Tests unbinding a factory
     */
    public function testUnbindingFactory()
    {
        $this->container->bindFactory($this->baseClass, function () {
            return new $this->concreteFoo;
        });
        $this->container->unbind($this->baseClass);
        $this->assertFalse($this->container->hasBinding($this->baseClass));
    }

    /**
     * Tests unbinding multiple interfaces
     */
    public function testUnbindingMultipleInterfaces()
    {
        $this->container->bindSingleton('foo', 'bar');
        $this->container->bindSingleton('baz', 'blah');
        $this->container->unbind(['foo', 'baz']);
        $this->assertFalse($this->container->hasBinding('foo'));
        $this->assertFalse($this->container->hasBinding('baz'));
    }

    /**
     * Tests unbinding a targeted binding
     */
    public function testUnbindingTargetedBinding()
    {
        $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            $container->bindPrototype($this->fooInterface, $this->concreteFoo);
        });
        $this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            $container->unbind($this->fooInterface);
        });
        $this->assertFalse($this->container->for($this->constructorWithIFoo, function (IContainer $container) {
            return $container->hasBinding($this->fooInterface);
        }));
    }

    /**
     * Tests unbinding a universal binding
     */
    public function testUnbindingUniversalBinding()
    {
        $this->container->bindPrototype($this->fooInterface, $this->concreteFoo);
        $this->container->unbind($this->fooInterface);
        $this->assertFalse($this->container->hasBinding($this->fooInterface));
    }

    /**
     * Tests universally binding an instance to an interface
     */
    public function testUniversallyBindingInstanceToInterface()
    {
        $instance = new $this->concreteFoo();
        $this->container->bindInstance($this->fooInterface, $instance);
        $this->assertSame($instance, $this->container->resolve($this->fooInterface));
        $this->assertNotSame($instance, $this->container->resolve($this->concreteFoo));
    }
}
