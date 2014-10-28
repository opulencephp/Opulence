<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the Monolog config
 */
namespace RDev\Applications\Configs;
use Monolog;
use Monolog\Handler;

class MonologConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests not specifying a Monolog handler
     */
    public function testNotSpecifyingMonologHandler()
    {
        $configArray = [
            "handlers" => [
                "main" => [
                    "type" => "Monolog\\Handler\\FingersCrossedHandler"
                ]
            ]
        ];
        $config = new MonologConfig($configArray);
        $this->assertInstanceOf("Monolog\\Handler\\FingersCrossedHandler", $config["handlers"]["main"]);
    }

    /**
     * Tests not specifying a Monolog handler type
     */
    public function testNotSpecifyingMonologHandlerType()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "handlers" => [
                "main" => [
                    "handler" => "Monolog\\Handler\\ErrorLogHandler"
                ]
            ]
        ];
        new MonologConfig($configArray);
    }

    /**
     * Tests specifying a Monolog handler type class that does not implement the handler interface
     */
    public function testSpecifyingHandlerTypeClassThatDoesNotImplementInterface()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "handlers" => [
                "main" => [
                    "type" => get_class($this),
                    "handler" => "Monolog\\Handler\\ErrorLogHandler"
                ]
            ]
        ];
        new MonologConfig($configArray);
    }

    /**
     * Tests specifying a error level for a Monolog handler
     */
    public function testSpecifyingMonologLevel()
    {
        $configArray = [
            "handlers" => [
                "main" => [
                    "type" => "Monolog\\Handler\\FingersCrossedHandler",
                    "handler" => "Monolog\\Handler\\ErrorLogHandler",
                    "level" => Monolog\Logger::CRITICAL
                ]
            ]
        ];
        $config = new MonologConfig($configArray);
        /** @var Handler\AbstractHandler $handler */
        $handler = $config["handlers"]["main"];
        $this->assertEquals(Monolog\Logger::DEBUG, $handler->getLevel());
    }

    /**
     * Tests specifying a Monolog type and handler classes
     */
    public function testSpecifyingMonologTypeAndHandlerClasses()
    {
        $configArray = [
            "handlers" => [
                "main" => [
                    "type" => "Monolog\\Handler\\FingersCrossedHandler",
                    "handler" => "Monolog\\Handler\\ErrorLogHandler"
                ]
            ]
        ];
        $config = new MonologConfig($configArray);
        $expectedType = new Handler\FingersCrossedHandler(new Handler\ErrorLogHandler());
        $this->assertEquals($expectedType, $config["handlers"]["main"]);
    }

    /**
     * Tests specifying a Monolog type class and handler object
     */
    public function testSpecifyingMonologTypeClassAndHandlerObject()
    {
        $handler = new Handler\ErrorLogHandler();
        $configArray = [
            "handlers" => [
                "main" => [
                    "type" => "Monolog\\Handler\\FingersCrossedHandler",
                    "handler" => $handler
                ]
            ]
        ];
        $config = new MonologConfig($configArray);
        $expectedType = new Handler\FingersCrossedHandler($handler);
        $this->assertEquals($expectedType, $config["handlers"]["main"]);
    }

    /**
     * Tests specifying a non-existent Monolog handler type class
     */
    public function testSpecifyingNonExistentMonologHandlerTypeClass()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "handlers" => [
                "main" => [
                    "type" => "RDev\\Does\\Not\\Exist",
                    "handler" => "Monolog\\Handler\\ErrorLogHandler"
                ]
            ]
        ];
        new MonologConfig($configArray);
    }
} 