<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the environment detector class
 */
namespace RDev\Applications\Environments;

class EnvironmentDetectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var EnvironmentDetector The detector to use in tests */
    private $detector = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->detector = new EnvironmentDetector();
    }

    /**
     * Tests registering an array of hosts
     */
    public function testRegisteringArrayOfHosts()
    {
        $hosts = [
            new Host("8.8.8.8", false),
            new Host(gethostname(), false)
        ];
        $this->detector->registerHost("development", $hosts);
        $this->assertEquals("development", $this->detector->detect());
    }

    /**
     * Tests registering an empty array
     */
    public function testRegisteringEmptyArray()
    {
        $this->detector->registerHost("foo", []);
        $this->assertEquals(Environment::PRODUCTION, $this->detector->detect());
    }

    /**
     * Tests registering multiple arrays of hosts
     */
    public function testRegisteringMultipleArraysOfHosts()
    {
        $developmentHosts = [
            new Host("8.8.8.8", false),
            new Host(gethostname(), false)
        ];
        $this->detector->registerHost("development", $developmentHosts);
        $stagingHosts = [
            new Host("8.8.8.2", false),
            new Host(gethostname(), false)
        ];
        $this->detector->registerHost("staging", $stagingHosts);
        $this->assertEquals("development", $this->detector->detect());
    }

    /**
     * Tests registering multiple hosts
     */
    public function testRegisteringMultipleHosts()
    {
        $host1 = new Host("8.8.8.8", false);
        $host2 = new Host(gethostname(), false);
        $host3 = new Host("8.8.8.2", false);
        $this->detector->registerHost("foo", $host1);
        $this->detector->registerHost("bar", $host2);
        $this->detector->registerHost("baz", $host3);
        $this->assertEquals("bar", $this->detector->detect());
    }

    /**
     * Tests registering a single host
     */
    public function testRegisteringSingleHost()
    {
        $this->detector->registerHost("development", new Host(gethostname(), false));
        $this->assertEquals("development", $this->detector->detect());
    }

    /**
     * Tests that the default environment is production
     */
    public function testThatDefaultEnvironmentIsProduction()
    {
        $this->assertEquals(Environment::PRODUCTION, $this->detector->detect());
    }

    /**
     * Tests a config with this server set to be on the development environment
     */
    public function testThisServerBeingDevelopmentEnvironment()
    {
        $this->detector->registerHost("development", new Host(gethostname(), false));
        $this->detector->registerHost("testing", new Host("8.8.8.2", false));
        $this->detector->registerHost("staging", new Host("8.8.8.8", false));
        $this->detector->registerHost("production", new Host("8.8.8.4", false));
        $this->assertEquals("development", $this->detector->detect());
    }

    /**
     * Tests a config with this server set to be on the production environment
     */
    public function testThisServerBeingProductionEnvironment()
    {
        $this->detector->registerHost("development", new Host("8.8.8.8", false));
        $this->detector->registerHost("testing", new Host("8.8.8.2", false));
        $this->detector->registerHost("staging", new Host("8.8.8.4", false));
        $this->detector->registerHost("production", new Host(gethostname(), false));
        $this->assertEquals("production", $this->detector->detect());
    }

    /**
     * Tests a config with this server set to be on the staging environment
     */
    public function testThisServerBeingStagingEnvironment()
    {
        $this->detector->registerHost("development", new Host("8.8.8.8", false));
        $this->detector->registerHost("testing", new Host("8.8.8.2", false));
        $this->detector->registerHost("staging", new Host(gethostname(), false));
        $this->detector->registerHost("production", new Host("8.8.8.4", false));
        $this->assertEquals("staging", $this->detector->detect());
    }

    /**
     * Tests a config with this server set to be on the testing environment
     */
    public function testThisServerBeingTestingEnvironment()
    {
        $this->detector->registerHost("development", new Host("8.8.8.8", false));
        $this->detector->registerHost("testing", new Host(gethostname(), false));
        $this->detector->registerHost("staging", new Host("8.8.8.2", false));
        $this->detector->registerHost("production", new Host("8.8.8.4", false));
        $this->assertEquals("testing", $this->detector->detect());
    }

    /**
     * Tests using a regular expression for the host
     */
    public function testUsingRegexForHost()
    {
        // Truncate the last character of the host
        $truncatedHost = substr(gethostname(), 0, -1);
        $this->detector->registerHost("development", new Host("8.8.8.8", false));
        $this->detector->registerHost("testing", new Host("8.8.8.2", false));
        $this->detector->registerHost("staging", new Host("/^" . preg_quote($truncatedHost, "/") . ".*$/", true));
        $this->detector->registerHost("production", new Host("8.8.8.4", false));
        $this->assertEquals("staging", $this->detector->detect());
    }
} 