<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the inversion of control container
 */
namespace RDev\Models\IoC;
use RDev\Tests\Models\IoC;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Container */
    private $container = null;
    /** @var string The name of the simple interface to use in tests */
    private $interfaceName = "RDev\\Tests\\Models\\IoC\\Mocks\\IFoo";
    /** @var string The name of the base class to use in tests */
    private $baseClassName = "RDev\\Tests\\Models\\IoC\\Mocks\\BaseClass";
    /** @var string The name of the class that implements the simple interface to use in tests */
    private $concreteClassName = "RDev\\Tests\\Models\\IoC\\Mocks\\Bar";
    /** @var string The name of a second class that implements the simple interface to use in tests */
    private $secondConcreteClassName = "RDev\\Tests\\Models\\IoC\\Mocks\\Blah";
    /** @var string The name of the class that accepts the simple interface in its constructor */
    private $constructorWithInterfaceName = "RDev\\Tests\\Models\\IoC\\Mocks\\ConstructorWithInterface";
    /** @var string The name of the class that accepts the simple concrete class in its constructor */
    private $constructorWithConcreteClassName = "RDev\\Tests\\Models\\IoC\\Mocks\\ConstructorWithConcreteClass";
    /** @var string The name of the class that accepts the simple concrete class in its constructor */
    private $constructorWithPrimitivesName = "RDev\\Tests\\Models\\IoC\\Mocks\\ConstructorWithPrimitives";
    /** @var string The name of the class that uses setters */
    private $constructorWithSettersName = "RDev\\Tests\\Models\\IoC\\Mocks\\ConstructorWithSetters";

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
        $this->container->bind($this->baseClassName, $this->concreteClassName);
        $object = $this->container->createSingleton($this->baseClassName);
        $this->assertInstanceOf($this->concreteClassName, $object);
    }

    /**
     * Tests binding to an interface
     */
    public function testBindingToInterface()
    {
        $this->container->bind($this->interfaceName, $this->concreteClassName);
        $object = $this->container->createSingleton($this->interfaceName);
        $this->assertInstanceOf($this->concreteClassName, $object);
    }

    /**
     * Tests calling setters
     */
    public function testCallingSetters()
    {
        $methodCalls = [
            "setFoo" => ["bar"]
        ];
        $object = $this->container->createSingleton($this->constructorWithSettersName, [], $methodCalls);
        $this->assertEquals("bar", $object->getFoo());
    }

    /**
     * Tests binding to an individual class
     */
    public function testClassBinding()
    {
        $this->container->bind($this->interfaceName, $this->secondConcreteClassName, $this->constructorWithInterfaceName);
        // Setup a universal binding which in theory should not be respected
        $this->container->bind($this->interfaceName, $this->concreteClassName);
        $object = $this->container->createSingleton($this->constructorWithInterfaceName);
        $this->assertInstanceOf($this->constructorWithInterfaceName, $object);
        $this->assertInstanceOf($this->secondConcreteClassName, $object->getFoo());
    }

    /**
     * Tests creating a new instance of a concrete class
     */
    public function testCreatingNewConcreteClass()
    {
        $object = $this->container->createSingleton($this->concreteClassName);
        $this->assertInstanceOf($this->concreteClassName, $object);
        $this->assertNotSame($object, $this->container->createNew($this->concreteClassName));
    }

    /**
     * Tests creating an object with a concrete class in its constructor
     */
    public function testCreatingObjectWithConcreteClassInConstructor()
    {
        $object = $this->container->createSingleton($this->constructorWithConcreteClassName);
        $this->assertInstanceOf($this->constructorWithConcreteClassName, $object);
        $this->assertInstanceOf($this->concreteClassName, $object->getFoo());
    }

    /**
     * Tests creating an object with an interface in its constructor
     */
    public function testCreatingObjectWithInterfaceInConstructor()
    {
        $this->container->bind($this->interfaceName, $this->concreteClassName);
        $object = $this->container->createSingleton($this->constructorWithInterfaceName);
        $this->assertInstanceOf($this->constructorWithInterfaceName, $object);
        $this->assertInstanceOf($this->concreteClassName, $object->getFoo());
    }

    /**
     * Tests creating an object with primitives in its constructor
     */
    public function testCreatingObjectWithPrimitivesInConstructor()
    {
        $this->container->bind($this->interfaceName, $this->concreteClassName);
        $object = $this->container->createSingleton($this->constructorWithPrimitivesName, ["foo"]);
        $this->assertInstanceOf($this->constructorWithPrimitivesName, $object);
        $this->assertEquals("foo", $object->getFoo());
    }

    /**
     * Tests creating a singleton instance of a concrete class
     */
    public function testCreatingSingletonConcreteClass()
    {
        $object = $this->container->createSingleton($this->concreteClassName);
        $this->assertInstanceOf($this->concreteClassName, $object);
        $this->assertSame($object, $this->container->createSingleton($this->concreteClassName));
    }

    /**
     * Tests that a singleton will always return the same instance
     */
    public function testSingletonAlwaysReturnsSameInstance()
    {
        $singletonObject = $this->container->createSingleton($this->concreteClassName);
        // Create a new object to make sure it didn't mess with the singleton instance
        $this->container->createNew($this->concreteClassName);
        $this->assertSame($singletonObject, $this->container->createSingleton($this->concreteClassName));
    }
} 