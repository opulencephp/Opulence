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
     * Tests that the monolog key is automatically set
     */
    public function testMonologKeyIsSetAutomatically()
    {
        $config = new ApplicationConfig([]);
        $expectedMonolog = [
            "handlers" => [
                "main" => new Handler\ErrorLogHandler()
            ]
        ];
        $this->assertEquals($expectedMonolog, $config["monolog"]);
    }

    /**
     * Tests not specifying a Monolog handler
     */
    public function testNotSpecifyingMonologHandler()
    {
        $configArray = [
            "monolog" => [
                "handlers" => [
                    "main" => [
                        "type" => "Monolog\\Handler\\FingersCrossedHandler"
                    ]
                ]
            ]
        ];
        $config = new ApplicationConfig($configArray);
        $this->assertInstanceOf("Monolog\\Handler\\FingersCrossedHandler", $config["monolog"]["handlers"]["main"]);
    }

    /**
     * Tests not specifying a Monolog handler type
     */
    public function testNotSpecifyingMonologHandlerType()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "monolog" => [
                "handlers" => [
                    "main" => [
                        "handler" => "Monolog\\Handler\\ErrorLogHandler"
                    ]
                ]
            ]
        ];
        new ApplicationConfig($configArray);
    }

    /**
     * Tests not specifying Monolog handlers
     */
    public function testNotSpecifyingMonologHandlers()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "monolog" => []
        ];
        new ApplicationConfig($configArray);
    }

    /**
     * Tests that the routing key is automatically set
     */
    public function testRoutingKeyIsSetAutomatically()
    {
        $config = new ApplicationConfig([]);
        $this->assertEquals([], $config["routing"]);
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
     * Tests specifying a Monolog handler type class that does not implement the handler interface
     */
    public function testSpecifyingHandlerTypeClassThatDoesNotImplementInterface()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "monolog" => [
                "handlers" => [
                    "main" => [
                        "type" => get_class($this),
                        "handler" => "Monolog\\Handler\\ErrorLogHandler"
                    ]
                ]
            ]
        ];
        new ApplicationConfig($configArray);
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

    /**
     * Tests specifying a error level for a Monolog handler
     */
    public function testSpecifyingMonologLevel()
    {
        $configArray = [
            "monolog" => [
                "handlers" => [
                    "main" => [
                        "type" => "Monolog\\Handler\\FingersCrossedHandler",
                        "handler" => "Monolog\\Handler\\ErrorLogHandler",
                        "level" => Monolog\Logger::CRITICAL
                    ]
                ]
            ]
        ];
        $config = new ApplicationConfig($configArray);
        /** @var Handler\AbstractHandler $handler */
        $handler = $config["monolog"]["handlers"]["main"];
        $this->assertEquals(Monolog\Logger::DEBUG, $handler->getLevel());
    }

    /**
     * Tests specifying a Monolog type and handler classes
     */
    public function testSpecifyingMonologTypeAndHandlerClasses()
    {
        $configArray = [
            "monolog" => [
                "handlers" => [
                    "main" => [
                        "type" => "Monolog\\Handler\\FingersCrossedHandler",
                        "handler" => "Monolog\\Handler\\ErrorLogHandler"
                    ]
                ]
            ]
        ];
        $config = new ApplicationConfig($configArray);
        $expectedType = new Handler\FingersCrossedHandler(new Handler\ErrorLogHandler());
        $this->assertEquals($expectedType, $config["monolog"]["handlers"]["main"]);
    }

    /**
     * Tests specifying a Monolog type class and handler object
     */
    public function testSpecifyingMonologTypeClassAndHandlerObject()
    {
        $handler = new Handler\ErrorLogHandler();
        $configArray = [
            "monolog" => [
                "handlers" => [
                    "main" => [
                        "type" => "Monolog\\Handler\\FingersCrossedHandler",
                        "handler" => $handler
                    ]
                ]
            ]
        ];
        $config = new ApplicationConfig($configArray);
        $expectedType = new Handler\FingersCrossedHandler($handler);
        $this->assertEquals($expectedType, $config["monolog"]["handlers"]["main"]);
    }

    /**
     * Tests specifying a non-existent Monolog handler type class
     */
    public function testSpecifyingNonExistentMonologHandlerTypeClass()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "monolog" => [
                "handlers" => [
                    "main" => [
                        "type" => "RDev\\Does\\Not\\Exist",
                        "handler" => "Monolog\\Handler\\ErrorLogHandler"
                    ]
                ]
            ]
        ];
        new ApplicationConfig($configArray);
    }

    /**
     * Tests that the targeted bindings key is set automatically
     */
    public function testTargetedBindingKeyIsSetAutomatically()
    {
        $bindings = [
            "bindings" => [
                "container" => new IoC\Container(),
                "universal" => []
            ]
        ];
        $config = new ApplicationConfig($bindings);
        $this->assertEquals([], $config["bindings"]["targeted"]);
    }

    /**
     * Tests that the universal bindings key is set automatically
     */
    public function testUniversalBindingKeyIsSetAutomatically()
    {
        $bindings = [
            "bindings" => [
                "container" => new IoC\Container(),
                "targeted" => []
            ]
        ];
        $config = new ApplicationConfig($bindings);
        $this->assertEquals([], $config["bindings"]["universal"]);
    }
} 