<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Tests the environment
 */
namespace RDev\Applications;
use RDev\Tests\Applications\Mocks;

class EnvironmentTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Environment The environment to use in tests */
    private $environment = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->environment = new Environment("foo");
    }

    /**
     * Tests getting the name
     */
    public function testGettingName()
    {
        $this->assertEquals("foo", $this->environment->getName());
    }

    /**
     * Tests getting a variable
     */
    public function testGettingVariable()
    {
        putenv("bar=baz");
        $this->assertEquals("baz", $this->environment->getVariable("bar"));
        $this->environment->setVariable("baz", "blah");
        $this->assertEquals("blah", $this->environment->getVariable("baz"));
    }

    /**
     * Tests checking if the application is running in a console
     */
    public function testIsRunningInConsole()
    {
        $isRunningInConsole = $this->environment->isRunningInConsole();

        if(php_sapi_name() == "cli")
        {
            $this->assertTrue($isRunningInConsole);
        }
        else
        {
            $this->assertFalse($isRunningInConsole);
        }
    }

    /**
     * Tests setting the name
     */
    public function testSettingName()
    {
        $this->environment->setName("foo");
        $this->assertEquals("foo", $this->environment->getName());
    }

    /**
     * Tests setting a variable
     */
    public function testSettingVariable()
    {
        $this->environment->setVariable("foo", "bar");
        $this->assertEquals("bar", getenv("foo"));
    }
}