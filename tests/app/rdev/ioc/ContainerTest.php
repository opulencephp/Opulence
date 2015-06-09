<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the dependency injection controller
 */
namespace RDev\IoC;
use RDev\Tests\IoC\Mocks\Bar;
use RDev\Tests\IoC\Mocks\ConstructorWithInterfaceAndSetters;
use RDev\Tests\IoC\Mocks\ConstructorWithMixOfConcreteClassesAndPrimitives;
use RDev\Tests\IoC\Mocks\ConstructorWithMixOfInterfacesAndPrimitives;
use RDev\Tests\IoC\Mocks\ConstructorWithReference;
use RDev\Tests\IoC\Mocks\ConstructorWithSetters;
use RDev\Tests\IoC\Mocks\IFoo;
use RDev\Tests\IoC\Mocks\MagicCallMethod;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Container The container to use in tests */
    private $container = null;
    /** @var string The name of the simple interface to use in tests */
    private $fooInterface = "RDev\\Tests\\IoC\\Mocks\\IFoo";
    /** @var string The name of the simple interface to use in tests */
    private $personInterface = "RDev\\Tests\\IoC\\Mocks\\IPerson";
    /** @var string The name of a class that implements IPerson */
    private $concretePerson = "RDev\\Tests\\IoC\\Mocks\\Dave";
    /** @var string The name of the base class to use in tests */
    private $baseClass = "RDev\\Tests\\IoC\\Mocks\\BaseClass";
    /** @var string The name of the class that implements IFoo to use in tests */
    private $concreteFoo = "RDev\\Tests\\IoC\\Mocks\\Bar";
    /** @var string The name of a second class that implements the IFoo to use in tests */
    private $secondConcreteIFoo = "RDev\\Tests\\IoC\\Mocks\\Blah";
    /** @var string The name of a another class that implements the IFoo to use in tests */
    private $concreteFooWithIPersonDependency = "RDev\\Tests\\IoC\\Mocks\\Foo";
    /** @var string The name of the class that accepts the IFoo in its constructor */
    private $constructorWithIFoo = "RDev\\Tests\\IoC\\Mocks\\ConstructorWithInterface";
    /** @var string The name of the class that accepts the concrete class in its constructor */
    private $constructorWithConcreteClass = "RDev\\Tests\\IoC\\Mocks\\ConstructorWithConcreteClass";
    /** @var string The name of the class that accepts a mix of interfaces and primitives in its constructor */
    private $constructorWithInterfacesAndPrimitives =
        "RDev\\Tests\\IoC\\Mocks\\ConstructorWithMixOfInterfacesAndPrimitives";
    /** @var string The name of the class that accepts a mix of class names and primitives in its constructor */
    private $constructorWithConcreteClassesAndPrimitives =
        "RDev\\Tests\\IoC\\Mocks\\ConstructorWithMixOfConcreteClassesAndPrimitives";
    /** @var string The name of the class that accepts the primitives in its constructor */
    private $constructorWithPrimitives = "RDev\\Tests\\IoC\\Mocks\\ConstructorWithPrimitives";
    /** @var string The name of the class that accepts primitives with default values in its constructor */
    private $constructorWithDefaultValuePrimitives =
        "RDev\\Tests\\IoC\\Mocks\\ConstructorWithDefaultValuePrimitives";
    /** @var string The name of the class that uses setters */
    private $constructorWithSetters = "RDev\\Tests\\IoC\\Mocks\\ConstructorWithSetters";
    /** @var string The name of the class that uses an interface in the constructor and setters */
    private $constructorWithIFooAndSetters = "RDev\\Tests\\IoC\\Mocks\\ConstructorWithInterfaceAndSetters";
    /** @var string The name of the class that takes in a reference */
    private $constructorWithReference = "RDev\\Tests\\IoC\\Mocks\\ConstructorWithReference";

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->container = new Container();
    }

    /**
     * Tests binding a targeted callback
     */
    public function testBindingTargetedCallback()
    {
        $this->container->bind($this->fooInterface, function ()
        {
            return new $this->concreteFoo;
        }, $this->constructorWithIFoo);
        $object = $this->container->makeShared($this->constructorWithIFoo);
        $this->assertInstanceOf($this->concreteFoo, $object->getFoo());
    }

    /**
     * Tests binding to an abstract class
     */
    public function testBindingToAbstractClass()
    {
        $this->container->bind($this->baseClass, $this->concreteFoo);
        $sharedInstance = $this->container->makeShared($this->baseClass);
        $this->assertInstanceOf($this->concreteFoo, $sharedInstance);
        $newInstance = $this->container->makeNew($this->baseClass);
        $this->assertInstanceOf($this->concreteFoo, $newInstance);
    }

    /**
     * Tests binding a universal callback
     */
    public function testBindingUniversalCallback()
    {
        $this->container->bind($this->fooInterface, function ()
        {
            return new $this->concreteFoo;
        });
        $sharedInstance = $this->container->makeShared($this->fooInterface);
        $newInstance = $this->container->makeNew($this->fooInterface);
        $this->assertTrue(is_callable($this->container->getBinding($this->fooInterface)));
        $this->assertInstanceOf($this->concreteFoo, $sharedInstance);
        $this->assertInstanceOf($this->concreteFoo, $newInstance);
        $this->assertNotSame($sharedInstance, $newInstance);
    }

    /**
     * Tests calling a method with primitive types
     */
    public function testCallingMethodWithPrimitiveTypes()
    {
        $instance = new ConstructorWithSetters();
        $this->container->call([$instance, "setPrimitive"], ["foo"]);
        $this->assertSame("foo", $instance->getPrimitive());
        $response = $this->container->call(
            function ($primitive)
            {
                return $primitive;
            },
            ["foo"]
        );
        $this->assertEquals("foo", $response);
    }

    /**
     * Tests calling a method with primitive types without specifying a value
     */
    public function testCallingMethodWithPrimitiveTypesWithoutSpecifyingValue()
    {
        $this->setExpectedException(IoCException::class);
        $instance = new ConstructorWithSetters();
        $this->container->call([$instance, "setPrimitive"]);
    }

    /**
     * Tests calling a method with type-hinted and primitive types
     */
    public function testCallingMethodWithTypeHintedAndPrimitiveTypes()
    {
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        $instance = new ConstructorWithSetters();
        $this->container->call([$instance, "setBoth"], ["foo"]);
        $this->assertInstanceOf($this->concreteFoo, $instance->getInterface());
        $this->assertSame("foo", $instance->getPrimitive());
        $response = $this->container->call(
            function (IFoo $interface, $primitive)
            {
                return get_class($interface) . ":" . $primitive;
            },
            ["foo"]
        );
        $this->assertEquals($this->concreteFoo . ":foo", $response);
    }

    /**
     * Tests calling a method with type hints
     */
    public function testCallingMethodWithTypeHints()
    {
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        $instance = new ConstructorWithSetters();
        $this->container->call([$instance, "setInterface"]);
        $this->assertInstanceOf($this->concreteFoo, $instance->getInterface());
        $response = $this->container->call(
            function (IFoo $interface)
            {
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
        $this->setExpectedException(IoCException::class);
        $instance = new ConstructorWithSetters();
        $this->container->call([$instance, "foobar"]);
    }

    /**
     * Tests calling a non-existent method and ignoring that it is missing
     */
    public function testCallingNonExistentMethodAndIgnoringThatItIsMissing()
    {
        $instance = new ConstructorWithSetters();
        $this->assertNull($this->container->call([$instance, "foobar"], [], true));
    }

    /**
     * Tests calling a non-existent method on a class that has a magic call method
     */
    public function testCallingNonExistentMethodOnClassThatHasMagicCallMethod()
    {
        $instance = new MagicCallMethod();
        $this->assertNull($this->container->call([$instance, "foobar"], [], true));
    }

    /**
     * Tests if a target-bound interface is bound
     */
    public function testCheckingIfTargetBoundInterfaceIsBound()
    {
        $this->container->bind($this->fooInterface, $this->concreteFoo, $this->constructorWithIFoo);
        $this->assertTrue($this->container->isBound($this->fooInterface, $this->constructorWithIFoo));
        $this->container->bind($this->fooInterface, function ()
        {
            return new $this->concreteFoo;
        }, $this->constructorWithIFoo);
        $this->assertTrue($this->container->isBound($this->fooInterface, $this->constructorWithIFoo));
    }

    /**
     * Tests if a universally bound interface is bound
     */
    public function testCheckingIfUniversallyBoundInterfaceIsBound()
    {
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        $this->assertTrue($this->container->isBound($this->fooInterface));
        $this->container->bind($this->fooInterface, function ()
        {
            return new $this->concreteFoo;
        });
        $this->assertTrue($this->container->isBound($this->fooInterface));
    }

    /**
     * Tests creating an interface without binding a concrete implementation
     */
    public function testCreatingInterfaceWithoutBinding()
    {
        $this->setExpectedException(IoCException::class);
        $this->container->makeNew($this->fooInterface);
    }

    /**
     * Tests creating a new instance with a concrete dependency
     */
    public function testCreatingNewInstanceWithConcreteDependency()
    {
        $newInstance = $this->container->makeNew($this->constructorWithConcreteClass);
        $this->assertInstanceOf($this->constructorWithConcreteClass, $newInstance);
    }

    /**
     * Tests creating a new instance with an interface in the constructor and setters
     */
    public function testCreatingNewInstanceWithInterfaceInConstructorAndSetters()
    {
        $tests = function ()
        {
            /** @var ConstructorWithInterfaceAndSetters $newInstance */
            $methodCalls = [
                "setSetterDependency" => []
            ];
            $newInstance = $this->container->makeNew($this->constructorWithIFooAndSetters, [], $methodCalls);
            $this->assertInstanceOf($this->concreteFoo, $newInstance->getConstructorDependency());
            $this->assertInstanceOf($this->concretePerson, $newInstance->getSetterDependency());
        };
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        $this->container->bind($this->personInterface, $this->concretePerson);
        $tests();
        $this->container->bind($this->fooInterface, function ()
        {
            return new $this->concreteFoo;
        });
        $this->container->bind($this->personInterface, function ()
        {
            return new $this->concretePerson;
        });
        $tests();
    }

    /**
     * Tests creating a new instance with setters
     */
    public function testCreatingNewInstanceWithSetters()
    {
        $tests = function ()
        {
            $methodCalls = [
                "setPrimitive" => ["myPrimitive"],
                "setInterface" => []
            ];
            /** @var ConstructorWithSetters $newInstance */
            $newInstance = $this->container->makeNew($this->constructorWithSetters, [], $methodCalls);
            $this->assertEquals("myPrimitive", $newInstance->getPrimitive());
            $this->assertInstanceOf($this->concreteFoo, $newInstance->getInterface());
        };
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        $tests();
        $this->container->bind($this->fooInterface, function ()
        {
            return new $this->concreteFoo;
        });
        $tests();
    }

    /**
     * Tests creating a new object with a constructor primitive
     */
    public function testCreatingNewObjectWithConstructorPrimitive()
    {
        $instance = $this->container->makeNew($this->constructorWithPrimitives, ["foo", "bar"]);
        $this->assertInstanceOf($this->constructorWithPrimitives, $instance);
        $this->assertNotSame($instance, $this->container->makeNew($this->constructorWithPrimitives, ["foo", "bar"]));
        $this->assertNotSame($instance, $this->container->makeShared($this->constructorWithPrimitives, ["foo", "bar"]));
    }

    /**
     * Tests creating a new object with an unset constructor primitive
     */
    public function testCreatingNewObjectWithUnsetConstructorPrimitive()
    {
        $this->setExpectedException(IoCException::class);
        $this->container->makeNew($this->constructorWithPrimitives);
    }

    /**
     * Tests creating a new object with an unset constructor primitive with a default value
     */
    public function testCreatingNewObjectWithUnsetConstructorPrimitiveWithDefaultValue()
    {
        $instance = $this->container->makeNew($this->constructorWithDefaultValuePrimitives, ["foo"]);
        $this->assertInstanceOf($this->constructorWithDefaultValuePrimitives, $instance);
        $this->assertNotSame($instance, $this->container->makeNew($this->constructorWithDefaultValuePrimitives,
            ["foo"]));
        $this->assertNotSame($instance, $this->container->makeShared($this->constructorWithDefaultValuePrimitives,
            ["foo"]));
    }

    /**
     * Tests creating a shared instance with a concrete dependency
     */
    public function testCreatingSharedInstanceWithConcreteDependency()
    {
        $sharedInstance = $this->container->makeShared($this->constructorWithConcreteClass);
        $this->assertInstanceOf($this->constructorWithConcreteClass, $sharedInstance);
    }

    /**
     * Tests creating a shared instance with a constructor primitive
     */
    public function testCreatingSharedInstanceWithConstructorPrimitive()
    {
        $instance = $this->container->makeShared($this->constructorWithPrimitives, ["foo", "bar"]);
        $this->assertInstanceOf($this->constructorWithPrimitives, $instance);
        $this->assertSame($instance, $this->container->makeShared($this->constructorWithPrimitives, ["foo", "bar"]));
    }

    /**
     * Tests creating a shared instance with an interface in the constructor and setters
     */
    public function testCreatingSharedInstanceWithInterfaceInConstructorAndSetters()
    {
        $tests = function ()
        {
            $methodCalls = [
                "setSetterDependency" => []
            ];
            /** @var ConstructorWithInterfaceAndSetters $sharedInstance */
            $sharedInstance = $this->container->makeShared($this->constructorWithIFooAndSetters, [], $methodCalls);
            $this->assertInstanceOf($this->concreteFoo, $sharedInstance->getConstructorDependency());
            $this->assertInstanceOf($this->concretePerson, $sharedInstance->getSetterDependency());
        };
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        $this->container->bind($this->personInterface, $this->concretePerson);
        $tests();
        $this->container->bind($this->fooInterface, function ()
        {
            return new $this->concreteFoo;
        });
        $this->container->bind($this->personInterface, function ()
        {
            return new $this->concretePerson;
        });
        $tests();
    }

    /**
     * Tests creating a shared instance with setters
     */
    public function testCreatingSharedInstanceWithSetters()
    {
        $tests = function ()
        {
            $methodCalls = [
                "setPrimitive" => ["myPrimitive"],
                "setInterface" => []
            ];
            /** @var ConstructorWithSetters $sharedInstance */
            $sharedInstance = $this->container->makeShared($this->constructorWithSetters, [], $methodCalls);
            $this->assertEquals("myPrimitive", $sharedInstance->getPrimitive());
            $this->assertInstanceOf($this->concreteFoo, $sharedInstance->getInterface());
        };
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        $tests();
        $this->container->bind($this->fooInterface, function ()
        {
            return new $this->concreteFoo;
        });
        $tests();
    }

    /**
     * Tests creating a shared instance object with an unset constructor primitive
     */
    public function testCreatingSharedInstanceWithUnsetConstructorPrimitive()
    {
        $this->setExpectedException(IoCException::class);
        $this->container->makeShared($this->constructorWithPrimitives);
    }

    /**
     * Tests creating a shared instance with an unset constructor primitive with a default value
     */
    public function testCreatingSharedInstanceWithUnsetConstructorPrimitiveWithDefaultValue()
    {
        $instance = $this->container->makeShared($this->constructorWithDefaultValuePrimitives, ["foo"]);
        $this->assertInstanceOf($this->constructorWithDefaultValuePrimitives, $instance);
        $this->assertSame($instance, $this->container->makeShared($this->constructorWithDefaultValuePrimitives,
            ["foo"]));
    }

    /**
     * Tests creating a class that has a dependency that has a dependency
     */
    public function testDependencyThatHasDependency()
    {
        $tests = function ()
        {
            $this->assertInstanceOf(
                $this->concreteFooWithIPersonDependency,
                $this->container->makeShared($this->fooInterface)
            );
            $this->assertInstanceOf(
                $this->concreteFooWithIPersonDependency,
                $this->container->makeNew($this->fooInterface)
            );
        };
        $this->container->bind($this->fooInterface, $this->concreteFooWithIPersonDependency);
        $this->container->bind($this->personInterface, $this->concretePerson);
        $tests();
        $this->container->bind($this->fooInterface, function ()
        {
            return new $this->concreteFooWithIPersonDependency(new $this->concretePerson);
        });
        $this->container->bind($this->personInterface, function ()
        {
            return new $this->concretePerson;
        });
        $tests();
    }

    /**
     * Tests creating a class that has a dependency that has a dependency without binding all dependencies
     */
    public function testDependencyThatHasDependencyWithoutBindingAllDependencies()
    {
        $this->setExpectedException(IoCException::class);
        $this->container->bind($this->fooInterface, $this->concreteFooWithIPersonDependency);
        $this->container->makeShared($this->fooInterface);
    }

    /**
     * Tests getting a targeted binding
     */
    public function testGettingTargetedBinding()
    {
        $this->container->bind($this->fooInterface, $this->concreteFoo, $this->constructorWithIFoo);
        $this->assertEquals($this->concreteFoo, $this->container->getBinding($this->fooInterface, $this->constructorWithIFoo));
        $this->assertNull($this->container->getBinding($this->fooInterface));
    }

    /**
     * Tests getting a targeted binding when no targeted binding exists but a universal one does
     */
    public function testGettingTargetedBindingWhenOneDoesNotExistButUniversalBindingExists()
    {
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        $this->assertEquals($this->concreteFoo, $this->container->getBinding($this->fooInterface, $this->constructorWithIFoo));
    }

    /**
     * Tests getting an unbound targeted binding
     */
    public function testGettingUnboundTargetedBinding()
    {
        $this->assertFalse($this->container->isBound($this->fooInterface, $this->constructorWithIFoo));
        $this->assertNull($this->container->getBinding($this->fooInterface, $this->constructorWithIFoo));
    }

    /**
     * Tests getting an unbound universal binding
     */
    public function testGettingUnboundUniversalBinding()
    {
        $this->assertFalse($this->container->isBound($this->fooInterface));
        $this->assertNull($this->container->getBinding($this->fooInterface));
    }

    /**
     * Tests getting a universal binding
     */
    public function testGettingUniversalBinding()
    {
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        $this->assertEquals($this->concreteFoo, $this->container->getBinding($this->fooInterface));
    }

    /**
     * Tests making a class that takes in a reference in its constructor
     */
    public function testMakingClassWithReferenceParameter()
    {
        $bar = new Bar();
        $this->container->bind($this->fooInterface, $bar);
        /** @var ConstructorWithReference $object */
        $object = $this->container->makeShared($this->constructorWithReference);
        $this->assertInstanceOf($this->constructorWithReference, $object);
        $this->assertSame($bar, $object->getFoo());
    }

    /**
     * Tests making a new non-existent class
     */
    public function testMakingNewNonExistentClass()
    {
        $this->setExpectedException(IoCException::class);
        $this->container->makeNew("DoesNotExist");
    }

    /**
     * Tests making a non-existent class
     */
    public function testMakingNonExistentClass()
    {
        $this->setExpectedException(IoCException::class);
        $this->container->make("DoesNotExist", false);
    }

    /**
     * Tests making an object
     */
    public function testMakingObject()
    {
        $tests = function ()
        {
            $newInstance = $this->container->make($this->constructorWithIFoo, true);
            $sharedInstance = $this->container->make($this->constructorWithIFoo, false);
            $this->assertInstanceOf($this->constructorWithIFoo, $newInstance);
            $this->assertInstanceOf($this->constructorWithIFoo, $sharedInstance);
            $this->assertNotSame($newInstance, $sharedInstance);
            $this->assertSame($sharedInstance, $this->container->make($this->constructorWithIFoo, false));
        };
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        $tests();
        $this->container->bind($this->fooInterface, function ()
        {
            return new $this->concreteFoo;
        });
        $tests();
    }

    /**
     * Tests making a shared non-existent class
     */
    public function testMakingSharedNonExistentClass()
    {
        $this->setExpectedException(IoCException::class);
        $this->container->makeShared("DoesNotExist");
    }

    /**
     * Tests instantiating a class with a mix of concrete classes and primitives in its constructor
     */
    public function testMixOfConcreteClassesAndPrimitivesInConstructor()
    {
        /** @var ConstructorWithMixOfConcreteClassesAndPrimitives $sharedInstance */
        $sharedInstance = $this->container->makeShared($this->constructorWithConcreteClassesAndPrimitives, [23]);
        /** @var ConstructorWithMixOfConcreteClassesAndPrimitives $newInstance */
        $newInstance = $this->container->makeNew($this->constructorWithConcreteClassesAndPrimitives, [23]);
        $this->assertInstanceOf($this->constructorWithConcreteClassesAndPrimitives, $sharedInstance);
        $this->assertInstanceOf($this->constructorWithConcreteClassesAndPrimitives, $newInstance);
        $this->assertEquals(23, $sharedInstance->getId());
        $this->assertEquals(23, $newInstance->getId());
    }

    /**
     * Tests instantiating a class with a mix of interfaces and primitives in its constructor
     */
    public function testMixOfInterfacesAndPrimitivesInConstructor()
    {
        $tests = function ()
        {
            /** @var ConstructorWithMixOfInterfacesAndPrimitives $sharedInstance */
            $sharedInstance = $this->container->makeShared($this->constructorWithInterfacesAndPrimitives, [23]);
            /** @var ConstructorWithMixOfInterfacesAndPrimitives $newInstance */
            $newInstance = $this->container->makeNew($this->constructorWithInterfacesAndPrimitives, [23]);
            $this->assertInstanceOf($this->constructorWithInterfacesAndPrimitives, $sharedInstance);
            $this->assertInstanceOf($this->constructorWithInterfacesAndPrimitives, $newInstance);
            $this->assertEquals(23, $sharedInstance->getId());
            $this->assertEquals(23, $newInstance->getId());
        };
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        $this->container->bind($this->personInterface, $this->concretePerson);
        $tests();
        $this->container->bind($this->fooInterface, function ()
        {
            return new $this->concreteFoo;
        });
        $this->container->bind($this->personInterface, function ()
        {
            return new $this->concretePerson;
        });
        $tests();
    }

    /**
     * Tests registering multiple targeted bindings
     */
    public function testMultipleTargetedBindings()
    {
        $this->container->bind(["foo", "bar"], $this->concreteFoo, "baz");
        $this->assertEquals($this->concreteFoo, $this->container->getBinding("foo", "baz"));
        $this->assertEquals($this->concreteFoo, $this->container->getBinding("bar", "baz"));
    }

    /**
     * Tests registering multiple universal bindings
     */
    public function testMultipleUniversalBindings()
    {
        $this->container->bind(["foo", "bar"], $this->concreteFoo);
        $this->assertEquals($this->concreteFoo, $this->container->getBinding("foo"));
        $this->assertEquals($this->concreteFoo, $this->container->getBinding("bar"));
    }

    /**
     * Tests new instances' dependencies are shared instances
     */
    public function testNewInstanceDependenciesAreShared()
    {
        $tests = function ()
        {
            /** @var ConstructorWithMixOfInterfacesAndPrimitives $newInstance1 */
            $newInstance1 = $this->container->makeNew($this->constructorWithInterfacesAndPrimitives, [23]);
            /** @var ConstructorWithMixOfInterfacesAndPrimitives $newInstance2 */
            $newInstance2 = $this->container->makeNew($this->constructorWithInterfacesAndPrimitives, [23]);
            $this->assertSame($newInstance1->getFoo(), $newInstance2->getFoo());
            $this->assertSame($newInstance1->getPerson(), $newInstance2->getPerson());
        };
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        $this->container->bind($this->personInterface, $this->concretePerson);
        $tests();
        $this->container->bind($this->fooInterface, function ()
        {
            return new $this->concreteFoo;
        });
        $this->container->bind($this->personInterface, function ()
        {
            return new $this->concretePerson;
        });
        $tests();
    }

    /**
     * Tests that shared instances are the same when using callbacks
     */
    public function testSharedInstancesAreSameWhenUsingCallbacks()
    {
        $this->container->bind($this->baseClass, function ()
        {
            return new $this->concreteFoo;
        });
        $sharedInstance1 = $this->container->makeShared($this->baseClass);
        $sharedInstance2 = $this->container->makeShared($this->baseClass);
        $this->assertSame($sharedInstance1, $sharedInstance2);
    }

    /**
     * Tests a targeted binding of an instance to an interface
     */
    public function testTargetedBindingOfInstanceToInterface()
    {
        $instance = new $this->concreteFoo();
        $this->container->bind($this->fooInterface, $instance, $this->constructorWithIFoo);
        // This universal binding should get NOT take precedence over the class binding
        $this->container->bind($this->fooInterface, $this->secondConcreteIFoo);
        $sharedInstance = $this->container->makeShared($this->constructorWithIFoo);
        $this->assertSame($instance, $sharedInstance->getFoo());
    }

    /**
     * Tests unbinding a callback
     */
    public function testUnbindingCallback()
    {
        $this->container->bind($this->baseClass, function ()
        {
            return new $this->concreteFoo;
        });
        $this->container->unbind($this->baseClass);
        $this->assertNull($this->container->getBinding($this->baseClass));
    }

    /**
     * Tests unbinding a targeted binding
     */
    public function testUnbindingTargetedBinding()
    {
        $this->container->bind($this->fooInterface, $this->concreteFoo, $this->constructorWithIFoo);
        $this->container->unbind($this->fooInterface, $this->constructorWithIFoo);
        $this->assertNull($this->container->getBinding($this->fooInterface, $this->constructorWithIFoo));
    }

    /**
     * Tests unbinding a universal binding
     */
    public function testUnbindingUniversalBinding()
    {
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        $this->container->unbind($this->fooInterface);
        $this->assertNull($this->container->getBinding($this->fooInterface));
    }

    /**
     * Tests universally binding an instance to an interface
     */
    public function testUniversallyBindingInstanceToInterface()
    {
        $instance = new $this->concreteFoo();
        $this->container->bind($this->fooInterface, $instance);
        $this->assertSame($instance, $this->container->makeShared($this->fooInterface));
        $this->assertSame($instance, $this->container->makeShared($this->concreteFoo));
    }
} 