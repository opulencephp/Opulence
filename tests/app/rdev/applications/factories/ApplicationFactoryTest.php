<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the application factory
 */
namespace RDev\Applications\Factories;
use RDev\Applications;
use RDev\Applications\Configs;
use RDev\Tests\Applications\Mocks;

class ApplicationFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var ApplicationFactory The factory to use to create applications */
    private $factory = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->factory = new ApplicationFactory();
    }

    /**
     * Tests creating Monolog
     */
    public function testCreatingMonolog()
    {
        $configArray = [
            "monolog" => [
                "handlers" => [
                    "main" => [
                        "type" => new Mocks\MonologHandler(),
                        "handler" => new Mocks\MonologHandler()
                    ]
                ]
            ]
        ];
        $config = new Configs\ApplicationConfig($configArray);
        $application = $this->factory->createFromConfig($config);
        $this->assertInstanceOf(
            "RDev\\Tests\\Applications\\Mocks\\MonologHandler",
            $application->getLogger()->getHandlers()[0]
        );
    }

    /**
     * Tests getting the session
     */
    public function testGettingSession()
    {
        $config = new Configs\ApplicationConfig([]);
        $application = $this->factory->createFromConfig($config);
        $this->assertSame($config["session"]["session"], $application->getSession());
    }

    /**
     * Tests getting the IoC container
     */
    public function testGettingTheContainer()
    {
        $config = new Configs\ApplicationConfig([]);
        $application = $this->factory->createFromConfig($config);
        $this->assertSame($config["ioc"]["container"], $application->getIoCContainer());
    }

    /**
     * Tests specifying the environment
     */
    public function testSpecifyingEnvironment()
    {
        $configArray = [
            "environment" => [
                "names" => [
                    "staging" => gethostname()
                ]
            ],
        ];
        $config = new Configs\ApplicationConfig($configArray);
        $application = $this->factory->createFromConfig($config);
        $this->assertEquals("staging", $application->getEnvironment());
    }
}