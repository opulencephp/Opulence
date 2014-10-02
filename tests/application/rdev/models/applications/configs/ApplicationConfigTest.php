<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the application config
 */
namespace RDev\Models\Applications\Configs;
use RDev\Models\IoC;
use RDev\Tests\Models\IoC\Mocks;

class ApplicationConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that the bindings key is automatically set
     */
    public function testBindingsKeyIsSetAutomatically()
    {
        $config = new ApplicationConfig([]);
        $expectedBindings = [
            "container" => new IoC\Container(),
            "universal" => [],
            "targeted" => []
        ];
        $this->assertEquals($expectedBindings, $config["bindings"]);
    }

    /**
     * Tests that the environment key is automatically set
     */
    public function testEnvironmentKeyIsSetAutomatically()
    {
        $config = new ApplicationConfig([]);
        $this->assertEquals([], $config["environment"]);
    }

    /**
     * Tests an invalid container type
     */
    public function testInvalidContainerType()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "bindings" => [
                "container" => 1
            ]
        ];
        new ApplicationConfig($configArray);
    }

    /**
     * Tests invalid targeted bindings
     */
    public function testInvalidTargetedBindings()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "bindings" => [
                "targeted" => "foo"
            ]
        ];
        new ApplicationConfig($configArray);
    }

    /**
     * Tests invalid universal bindings
     */
    public function testInvalidUniversalBindings()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "bindings" => [
                "universal" => "foo"
            ]
        ];
        new ApplicationConfig($configArray);
    }

    /**
     * Tests that the router key is automatically set
     */
    public function testRouterKeyIsSetAutomatically()
    {
        $config = new ApplicationConfig([]);
        $this->assertEquals([], $config["router"]);
    }

    /**
     * Tests a single invalid targeted binding
     */
    public function testSingleInvalidTargetedBinding()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "bindings" => [
                "targeted" => [
                    "foo" => "bar"
                ]
            ]
        ];
        new ApplicationConfig($configArray);
    }

    /**
     * Tests specifying a container class
     */
    public function testSpecifyingContainerClass()
    {
        $containerClass = "RDev\\Tests\\Models\\IoC\\Mocks\\Container";
        $configArray = [
            "bindings" => [
                "container" => $containerClass
            ]
        ];
        $config = new ApplicationConfig($configArray);
        $this->assertInstanceOf($containerClass, $config["bindings"]["container"]);
    }

    /**
     * Tests specifyingContainerClassName
     */
    public function testSpecifyingContainerClassNAme()
    {
        $containerClass = "RDev\\Tests\\Models\\IoC\\Mocks\\Container";
        $configArray = [
            "bindings" => [
                "container" => $containerClass
            ]
        ];
        $config = new ApplicationConfig($configArray);
        $this->assertEquals(new $containerClass(), $config["bindings"]["container"]);
    }

    /**
     * Tests specifying a container class that does not implement IContainer
     */
    public function testSpecifyingContainerClassThatDoesNotImplementIContainer()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "bindings" => [
                "container" => get_class($this)
            ]
        ];
        new ApplicationConfig($configArray);
    }

    /**
     * Tests specifying a container object
     */
    public function testSpecifyingContainerObject()
    {
        $container = new Mocks\Container();
        $configArray = [
            "bindings" => [
                "container" => $container
            ]
        ];
        $config = new ApplicationConfig($configArray);
        $this->assertSame($container, $config["bindings"]["container"]);
    }

    /**
     * Tests specifying an invalid container class
     */
    public function testSpecifyingInvalidContainerClass()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "bindings" => [
                "container" => "RDev\\Class\\That\\Does\\Not\\Exist"
            ]
        ];
        new ApplicationConfig($configArray);
    }
} 