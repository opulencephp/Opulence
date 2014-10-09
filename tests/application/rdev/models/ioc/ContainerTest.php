<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the dependency injection controller
 */
namespace RDev\Models\IoC;
use RDev\Tests\Models\IoC\Mocks;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Container The container to use in tests */
    private $container = null;
    /** @var string The name of the simple interface to use in tests */
    private $fooInterface = "RDev\\Tests\\Models\\IoC\\Mocks\\IFoo";
    /** @var string The name of the simple interface to use in tests */
    private $personInterface = "RDev\\Tests\\Models\\IoC\\Mocks\\IPerson";
    /** @var string The name of a class that implements IPerson */
    private $concretePerson = "RDev\\Tests\\Models\\IoC\\Mocks\\Dave";
    /** @var string The name of the base class to use in tests */
    private $baseClass = "RDev\\Tests\\Models\\IoC\\Mocks\\BaseClass";
    /** @var string The name of the class that implements IFoo to use in tests */
    private $concreteFoo = "RDev\\Tests\\Models\\IoC\\Mocks\\Bar";
    /** @var string The name of a second class that implements the IFoo to use in tests */
    private $secondConcreteIFoo = "RDev\\Tests\\Models\\IoC\\Mocks\\Blah";
    /** @var string The name of a another class that implements the IFoo to use in tests */
    private $concreteFooWithIPersonDependency = "RDev\\Tests\\Models\\IoC\\Mocks\\Foo";
    /** @var string The name of the class that accepts the IFoo in its constructor */
    private $constructorWithIFoo = "RDev\\Tests\\Models\\IoC\\Mocks\\ConstructorWithInterface";
    /** @var string The name of the class that accepts the concrete class in its constructor */
    private $constructorWithConcreteClass = "RDev\\Tests\\Models\\IoC\\Mocks\\ConstructorWithConcreteClass";
    /** @var string The name of the class that accepts a mix of interfaces and primitives in its constructor */
    private $constructorWithInterfacesAndPrimitives =
        "RDev\\Tests\\Models\\IoC\\Mocks\\ConstructorWithMixOfInterfacesAndPrimitives";
    /** @var string The name of the class that accepts a mix of class names and primitives in its constructor */
    private $constructorWithConcreteClassesAndPrimitives =
        "RDev\\Tests\\Models\\IoC\\Mocks\\ConstructorWithMixOfConcreteClassesAndPrimitives";
    /** @var string The name of the class that accepts the primitives in its constructor */
    private $constructorWithPrimitives = "RDev\\Tests\\Models\\IoC\\Mocks\\ConstructorWithPrimitives";
    /** @var string The name of the class that accepts primitives with default values in its constructor */
    private $constructorWithDefaultValuePrimitives =
        "RDev\\Tests\\Models\\IoC\\Mocks\\ConstructorWithDefaultValuePrimitives";
    /** @var string The name of the class that uses setters */
    private $constructorWithSetters = "RDev\\Tests\\Models\\IoC\\Mocks\\ConstructorWithSetters";
    /** @var string The name of the class that uses an interface in the constructor and setters */
    private $constructorWithIFooAndSetters = "RDev\\Tests\\Models\\IoC\\Mocks\\ConstructorWithInterfaceAndSetters";

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->container = new Container();
    }

    /**
     * Tests binding to an abstract class
     */
    public function testBindingToAbstractClass()
    {
        $this->container->bind($this->baseClass, $this->concreteFoo);
        $singleton = $this->container->createSingleton($this->baseClass);
        $this->assertInstanceOf($this->concreteFoo, $singleton);
        $newInstance = $this->container->createNew($this->baseClass);
        $this->assertInstanceOf($this->concreteFoo, $newInstance);
    }

    /**
     * Tests creating an interface without binding a concrete implementation
     */
    public function testCreatingInterfaceWithoutBinding()
    {
        $this->setExpectedException("RDev\\Models\\IoC\\Exceptions\\IoCException");
        $this->container->createNew($this->fooInterface);
    }

    /**
     * Tests creating a new instance with a concrete dependency
     */
    public function testCreatingNewInstanceWithConcreteDependency()
    {
        $newInstance = $this->container->createNew($this->constructorWithConcreteClass);
        $this->assertInstanceOf($this->constructorWithConcreteClass, $newInstance);
    }

    /**
     * Tests creating a new instance with an interface in the constructor and setters
     */
    public function testCreatingNewInstanceWithInterfaceInConstructorAndSetters()
    {
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        $this->container->bind($this->personInterface, $this->concretePerson);
        $methodCalls = [
            "setSetterDependency" => []
        ];
        /** @var Mocks\ConstructorWithInterfaceAndSetters $newInstance */
        $newInstance = $this->container->createNew($this->constructorWithIFooAndSetters, [], $methodCalls);
        $this->assertInstanceOf($this->concreteFoo, $newInstance->getConstructorDependency());
        $this->assertInstanceOf($this->concretePerson, $newInstance->getSetterDependency());
    }

    /**
     * Tests creating a new instance with setters
     */
    public function testCreatingNewInstanceWithSetters()
    {
        $methodCalls = [
            "setPrimitive" => ["myPrimitive"],
            "setDependency" => []
        ];
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        /** @var Mocks\ConstructorWithSetters $newInstance */
        $newInstance = $this->container->createNew($this->constructorWithSetters, [], $methodCalls);
        $this->assertEquals("myPrimitive", $newInstance->getPrimitive());
        $this->assertInstanceOf($this->concreteFoo, $newInstance->getDependency());
    }

    /**
     * Tests creating a new object with a constructor primitive
     */
    public function testCreatingNewObjectWithConstructorPrimitive()
    {
        $instance = $this->container->createNew($this->constructorWithPrimitives, ["foo", "bar"]);
        $this->assertInstanceOf($this->constructorWithPrimitives, $instance);
        $this->assertNotSame($instance, $this->container->createNew($this->constructorWithPrimitives, ["foo", "bar"]));
        $this->assertNotSame($instance, $this->container->createSingleton($this->constructorWithPrimitives, ["foo", "bar"]));
    }

    /**
     * Tests creating a new object with an unset constructor primitive
     */
    public function testCreatingNewObjectWithUnsetConstructorPrimitive()
    {
        $this->setExpectedException("RDev\\Models\\IoC\\Exceptions\\IoCException");
        $this->container->createNew($this->constructorWithPrimitives);
    }

    /**
     * Tests creating a new object with an unset constructor primitive with a default value
     */
    public function testCreatingNewObjectWithUnsetConstructorPrimitiveWithDefaultValue()
    {
        $instance = $this->container->createNew($this->constructorWithDefaultValuePrimitives, ["foo"]);
        $this->assertInstanceOf($this->constructorWithDefaultValuePrimitives, $instance);
        $this->assertNotSame($instance, $this->container->createNew($this->constructorWithDefaultValuePrimitives,
            ["foo"]));
        $this->assertNotSame($instance, $this->container->createSingleton($this->constructorWithDefaultValuePrimitives,
            ["foo"]));
    }

    /**
     * Tests creating a singleton with a constructor primitive
     */
    public function testCreatingSingleWithConstructorPrimitive()
    {
        $instance = $this->container->createSingleton($this->constructorWithPrimitives, ["foo", "bar"]);
        $this->assertInstanceOf($this->constructorWithPrimitives, $instance);
        $this->assertSame($instance, $this->container->createSingleton($this->constructorWithPrimitives, ["foo", "bar"]));
    }

    /**
     * Tests creating a singleton with an unset constructor primitive with a default value
     */
    public function testCreatingSingleWithUnsetConstructorPrimitiveWithDefaultValue()
    {
        $instance = $this->container->createSingleton($this->constructorWithDefaultValuePrimitives, ["foo"]);
        $this->assertInstanceOf($this->constructorWithDefaultValuePrimitives, $instance);
        $this->assertSame($instance, $this->container->createSingleton($this->constructorWithDefaultValuePrimitives,
            ["foo"]));
    }

    /**
     * Tests creating a singleton with a concrete dependency
     */
    public function testCreatingSingletonWithConcreteDependency()
    {
        $singleton = $this->container->createSingleton($this->constructorWithConcreteClass);
        $this->assertInstanceOf($this->constructorWithConcreteClass, $singleton);
    }

    /**
     * Tests creating a singleton with an interface in the constructor and setters
     */
    public function testCreatingSingletonWithInterfaceInConstructorAndSetters()
    {
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        $this->container->bind($this->personInterface, $this->concretePerson);
        $methodCalls = [
            "setSetterDependency" => []
        ];
        /** @var Mocks\ConstructorWithInterfaceAndSetters $singleton */
        $singleton = $this->container->createSingleton($this->constructorWithIFooAndSetters, [], $methodCalls);
        $this->assertInstanceOf($this->concreteFoo, $singleton->getConstructorDependency());
        $this->assertInstanceOf($this->concretePerson, $singleton->getSetterDependency());
    }

    /**
     * Tests creating a singleton with setters
     */
    public function testCreatingSingletonWithSetters()
    {
        $methodCalls = [
            "setPrimitive" => ["myPrimitive"],
            "setDependency" => []
        ];
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        /** @var Mocks\ConstructorWithSetters $singleton */
        $singleton = $this->container->createSingleton($this->constructorWithSetters, [], $methodCalls);
        $this->assertEquals("myPrimitive", $singleton->getPrimitive());
        $this->assertInstanceOf($this->concreteFoo, $singleton->getDependency());
    }

    /**
     * Tests creating a singleton object with an unset constructor primitive
     */
    public function testCreatingSingletonWithUnsetConstructorPrimitive()
    {
        $this->setExpectedException("RDev\\Models\\IoC\\Exceptions\\IoCException");
        $this->container->createSingleton($this->constructorWithPrimitives);
    }

    /**
     * Tests creating a class that has a dependency that has a dependency
     */
    public function testDependencyThatHasDependency()
    {
        $this->container->bind($this->fooInterface, $this->concreteFooWithIPersonDependency);
        $this->container->bind($this->personInterface, $this->concretePerson);
        $this->assertInstanceOf(
            $this->concreteFooWithIPersonDependency,
            $this->container->createSingleton($this->fooInterface)
        );
        $this->assertInstanceOf(
            $this->concreteFooWithIPersonDependency,
            $this->container->createNew($this->fooInterface)
        );
    }

    /**
     * Tests creating a class that has a dependency that has a dependency without binding all dependencies
     */
    public function testDependencyThatHasDependencyWithoutBindingAllDependencies()
    {
        $this->setExpectedException("RDev\\Models\\IoC\\Exceptions\\IoCException");
        $this->container->bind($this->fooInterface, $this->concreteFooWithIPersonDependency);
        $this->container->createSingleton($this->fooInterface);
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
        $this->assertNull($this->container->getBinding($this->fooInterface, $this->constructorWithIFoo));
    }

    /**
     * Tests getting an unbound universal binding
     */
    public function testGettingUnboundUniversalBinding()
    {
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
     * Tests instantiating a class with a mix of concrete classes and primitives in its constructor
     */
    public function testMixOfConcreteClassesAndPrimitivesInConstructor()
    {
        /** @var Mocks\ConstructorWithMixOfConcreteClassesAndPrimitives $singleton */
        $singleton = $this->container->createSingleton($this->constructorWithConcreteClassesAndPrimitives, [23]);
        /** @var Mocks\ConstructorWithMixOfConcreteClassesAndPrimitives $newInstance */
        $newInstance = $this->container->createNew($this->constructorWithConcreteClassesAndPrimitives, [23]);
        $this->assertInstanceOf($this->constructorWithConcreteClassesAndPrimitives, $singleton);
        $this->assertInstanceOf($this->constructorWithConcreteClassesAndPrimitives, $newInstance);
        $this->assertEquals(23, $singleton->getId());
        $this->assertEquals(23, $newInstance->getId());
    }

    /**
     * Tests instantiating a class with a mix of interfaces and primitives in its constructor
     */
    public function testMixOfInterfacesAndPrimitivesInConstructor()
    {
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        $this->container->bind($this->personInterface, $this->concretePerson);
        /** @var Mocks\ConstructorWithMixOfInterfacesAndPrimitives $singleton */
        $singleton = $this->container->createSingleton($this->constructorWithInterfacesAndPrimitives, [23]);
        /** @var Mocks\ConstructorWithMixOfInterfacesAndPrimitives $newInstance */
        $newInstance = $this->container->createNew($this->constructorWithInterfacesAndPrimitives, [23]);
        $this->assertInstanceOf($this->constructorWithInterfacesAndPrimitives, $singleton);
        $this->assertInstanceOf($this->constructorWithInterfacesAndPrimitives, $newInstance);
        $this->assertEquals(23, $singleton->getId());
        $this->assertEquals(23, $newInstance->getId());
    }

    /**
     * Tests new instances' dependencies are not singletons
     */
    public function testNewInstanceDependenciesAreNotSingletons()
    {
        $this->container->bind($this->fooInterface, $this->concreteFoo);
        $this->container->bind($this->personInterface, $this->concretePerson);
        /** @var Mocks\ConstructorWithMixOfInterfacesAndPrimitives $newInstance1 */
        $newInstance1 = $this->container->createNew($this->constructorWithInterfacesAndPrimitives, [23]);
        /** @var Mocks\ConstructorWithMixOfInterfacesAndPrimitives $newInstance2 */
        $newInstance2 = $this->container->createNew($this->constructorWithInterfacesAndPrimitives, [23]);
        $this->assertNotSame($newInstance1->getFoo(), $newInstance2->getFoo());
        $this->assertNotSame($newInstance1->getPerson(), $newInstance2->getPerson());
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
        $singleton = $this->container->createSingleton($this->constructorWithIFoo);
        $this->assertSame($instance, $singleton->getFoo());
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
        $this->assertSame($instance, $this->container->createSingleton($this->fooInterface));
        $this->assertSame($instance, $this->container->createSingleton($this->concreteFoo));
    }
} 