<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the application config
 */
namespace RDev\Models\Applications\Configs;
use Monolog;
use Monolog\Handler;
use RDev\Models\IoC;
use RDev\Models\Routing;

class ApplicationConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that the environment key is automatically set
     */
    public function testEnvironmentKeyIsSetAutomatically()
    {
        $config = new ApplicationConfig([]);
        $this->assertEquals([], $config["environment"]->toArray());
        $this->assertInstanceOf("RDev\\Models\\Applications\\Configs\\EnvironmentConfig", $config["environment"]);
    }

    /**
     * Tests specifying an invalid environment config
     */
    public function testInvalidEnvironmentConfig()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "environment" => [
                "production" => 1
            ]
        ];
        new ApplicationConfig($configArray);
    }

    /**
     * Tests specifying an invalid IoC config
     */
    public function testInvalidIoCConfig()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "ioc" => [
                "container" => "RDev\\Class\\That\\Does\\Not\\Exist"
            ]
        ];
        new ApplicationConfig($configArray);
    }

    /**
     * Tests specifying an invalid Monolog config
     */
    public function testInvalidMonologConfig()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "monolog" => [
                "foo" => "bar"
            ]
        ];
        new ApplicationConfig($configArray);
    }

    /**
     * Tests specifying an invalid routing config
     */
    public function testInvalidRoutingConfig()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "routing" => [
                "compiler" => 1
            ]
        ];
        new ApplicationConfig($configArray);
    }

    /**
     * Tests that the IoC key is automatically set
     */
    public function testIoCKeyIsSetAutomatically()
    {
        $config = new ApplicationConfig([]);
        $expectedIoCConfigArray = [
            "container" => new IoC\Container(),
            "universal" => [],
            "targeted" => []
        ];
        $this->assertEquals($expectedIoCConfigArray, $config["ioc"]->toArray());
        $this->assertInstanceOf("RDev\\Models\\IoC\\Configs\\IoCConfig", $config["ioc"]);
    }

    /**
     * Tests that the monolog key is automatically set
     */
    public function testMonologKeyIsSetAutomatically()
    {
        $config = new ApplicationConfig([]);
        $expectedMonologConfigArray = [
            "handlers" => [
                "main" => new Handler\ErrorLogHandler()
            ]
        ];
        $this->assertEquals($expectedMonologConfigArray, $config["monolog"]->toArray());
        $this->assertInstanceOf("RDev\\Models\\Applications\\Configs\\MonologConfig", $config["monolog"]);
    }

    /**
     * Tests that the routing key is automatically set
     */
    public function testRoutingKeyIsSetAutomatically()
    {
        $config = new ApplicationConfig([]);
        $expectedConfigArray = [
            "compiler" => new Routing\RouteCompiler(),
            "routes" => [],
            "groups" => []
        ];
        $this->assertEquals($expectedConfigArray, $config["routing"]->toArray());
        $this->assertInstanceOf("RDev\\Models\\Routing\\Configs\\RouterConfig", $config["routing"]);
    }
} 