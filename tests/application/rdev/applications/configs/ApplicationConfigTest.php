<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the application config
 */
namespace RDev\Applications\Configs;
use Monolog;
use Monolog\Handler;
use RDev\IoC;
use RDev\Routing;
use RDev\Sessions;

class ApplicationConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that the bootstrappers key is automatically set
     */
    public function testBootstrappersKeyIsSetAutomatically()
    {
        $config = new ApplicationConfig([]);
        $this->assertTrue(is_array($config["bootstrappers"]));
    }

    /**
     * Tests that the environment key is automatically set
     */
    public function testEnvironmentKeyIsSetAutomatically()
    {
        $config = new ApplicationConfig([]);
        $this->assertEquals([], $config["environment"]->getArrayCopy());
        $this->assertInstanceOf("RDev\\Applications\\Configs\\EnvironmentConfig", $config["environment"]);
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
        $this->assertEquals($expectedIoCConfigArray, $config["ioc"]->getArrayCopy());
        $this->assertInstanceOf("RDev\\IoC\\Configs\\IoCConfig", $config["ioc"]);
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
        $this->assertEquals($expectedMonologConfigArray, $config["monolog"]->getArrayCopy());
        $this->assertInstanceOf("RDev\\Applications\\Configs\\MonologConfig", $config["monolog"]);
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
        $this->assertEquals($expectedConfigArray, $config["routing"]->getArrayCopy());
        $this->assertInstanceOf("RDev\\Routing\\Configs\\RouterConfig", $config["routing"]);
    }

    /**
     * Tests specifying a session class that does not exist
     */
    public function testSessionClassThatDoesNotExist()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "session" => "RDev\\Class\\That\\Does\\Not\\Exist"
        ];
        new ApplicationConfig($configArray);
    }

    /**
     * Tests specifying a session class that does not implement the correct interface
     */
    public function testSessionClassThatDoesNotImplementCorrectInterface()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "session" => get_class($this)
        ];
        new ApplicationConfig($configArray);
    }

    /**
     * Tests specifying a valid session class
     */
    public function testValidSessionClass()
    {
        $configArray = [
            "session" => "RDev\\Sessions\\Session"
        ];
        $config = new ApplicationConfig($configArray);
        $this->assertInstanceOf("RDev\\Sessions\\Session", $config["session"]);
    }

    /**
     * Tests specifying a valid session object
     */
    public function testValidSessionObject()
    {
        $session = new Sessions\Session();
        $configArray = [
            "session" => $session
        ];
        $config = new ApplicationConfig($configArray);
        $this->assertSame($session, $config["session"]);
    }
} 