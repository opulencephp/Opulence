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
    /** @var Environment */
    private $environment = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->environment = new Environment(new Mocks\EnvironmentDetector());
    }

    /**
     * Tests getting the name
     */
    public function testGettingName()
    {
        $this->assertEquals(Environment::PRODUCTION, $this->environment->getName());
    }

    /**
     * Tests getting a variable
     */
    public function testGettingVariable()
    {
        putenv("bar=baz");
        $this->assertEquals("baz", $this->environment->getVar("bar"));
        $this->environment->setVar("baz", "blah");
        $this->assertEquals("blah", $this->environment->getVar("baz"));
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
        $this->environment->setVar("foo", "bar");
        $this->assertEquals("bar", getenv("foo"));
    }
}