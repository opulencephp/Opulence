<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the bindings config
 */
namespace RDev\IoC\Configs;
use RDev\IoC;
use RDev\Tests\IoC\Mocks;

class IoCConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests an invalid container type
     */
    public function testInvalidContainerType()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "container" => 1
        ];
        new IoCConfig($configArray);
    }

    /**
     * Tests invalid targeted bindings
     */
    public function testInvalidTargetedBindings()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "targeted" => "foo"
        ];
        new IoCConfig($configArray);
    }

    /**
     * Tests invalid universal bindings
     */
    public function testInvalidUniversalBindings()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "universal" => "foo"
        ];
        new IoCConfig($configArray);
    }

    /**
     * Tests passing an instance to a targeted binding
     */
    public function testPassingInstanceToTargetedBinding()
    {
        $configArray = [
            "targeted" => [
                "Foo" => [
                    get_class($this) => $this
                ]
            ]
        ];
        $config = new IoCConfig($configArray);
        $this->assertSame($this, $config["targeted"]["Foo"][get_class($this)]);
    }

    /**
     * Tests passing an instance to a universal binding
     */
    public function testPassingInstanceToUniversalBinding()
    {
        $configArray = [
            "universal" => [
                get_class($this) => $this
            ]
        ];
        $config = new IoCConfig($configArray);
        $this->assertSame($this, $config["universal"][get_class($this)]);
    }

    /**
     * Tests a single invalid targeted binding
     */
    public function testSingleInvalidTargetedBinding()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "targeted" => [
                "foo" => "bar"
            ]
        ];
        new IoCConfig($configArray);
    }

    /**
     * Tests specifying a container class
     */
    public function testSpecifyingContainerClass()
    {
        $containerClass = "RDev\\Tests\\IoC\\Mocks\\Container";
        $configArray = [
            "container" => $containerClass
        ];
        $config = new IoCConfig($configArray);
        $this->assertInstanceOf($containerClass, $config["container"]);
    }

    /**
     * Tests specifyingContainerClassName
     */
    public function testSpecifyingContainerClassNAme()
    {
        $containerClass = "RDev\\Tests\\IoC\\Mocks\\Container";
        $configArray = [
            "container" => $containerClass
        ];
        $config = new IoCConfig($configArray);
        $this->assertEquals(new $containerClass(), $config["container"]);
    }

    /**
     * Tests specifying a container class that does not implement IContainer
     */
    public function testSpecifyingContainerClassThatDoesNotImplementIContainer()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "container" => get_class($this)
        ];
        new IoCConfig($configArray);
    }

    /**
     * Tests specifying a container object
     */
    public function testSpecifyingContainerObject()
    {
        $container = new Mocks\Container();
        $configArray = [
            "container" => $container
        ];
        $config = new IoCConfig($configArray);
        $this->assertSame($container, $config["container"]);
    }

    /**
     * Tests specifying an invalid container class
     */
    public function testSpecifyingInvalidContainerClass()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "container" => "RDev\\Class\\That\\Does\\Not\\Exist"
        ];
        new IoCConfig($configArray);
    }

    /**
     * Tests that the targeted bindings key is set automatically
     */
    public function testTargetedBindingKeyIsSetAutomatically()
    {
        $configArray = [
            "container" => new IoC\Container(),
            "universal" => []
        ];
        $config = new IoCConfig($configArray);
        $this->assertEquals([], $config["targeted"]);
    }

    /**
     * Tests that the universal bindings key is set automatically
     */
    public function testUniversalBindingKeyIsSetAutomatically()
    {
        $configArray = [
            "container" => new IoC\Container(),
            "targeted" => []
        ];
        $config = new IoCConfig($configArray);
        $this->assertEquals([], $config["universal"]);
    }
} 