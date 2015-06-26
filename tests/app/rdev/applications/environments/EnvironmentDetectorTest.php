<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the environment detector class
 */
namespace RDev\Applications\Environments;
use RDev\Applications\Environments\Hosts\HostName;
use RDev\Applications\Environments\Hosts\HostRegex;

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
            new HostName("8.8.8.8"),
            new HostName(gethostname())
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
            new HostName("8.8.8.8"),
            new HostName(gethostname())
        ];
        $this->detector->registerHost("development", $developmentHosts);
        $stagingHosts = [
            new HostName("8.8.8.2"),
            new HostName(gethostname())
        ];
        $this->detector->registerHost("staging", $stagingHosts);
        $this->assertEquals("development", $this->detector->detect());
    }

    /**
     * Tests registering multiple hosts
     */
    public function testRegisteringMultipleHosts()
    {
        $host1 = new HostName("8.8.8.8");
        $host2 = new HostName(gethostname());
        $host3 = new HostName("8.8.8.2");
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
        $this->detector->registerHost("development", new HostName(gethostname()));
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
        $this->detector->registerHost("development", new HostName(gethostname()));
        $this->detector->registerHost("testing", new HostName("8.8.8.2"));
        $this->detector->registerHost("staging", new HostName("8.8.8.8"));
        $this->detector->registerHost("production", new HostName("8.8.8.4"));
        $this->assertEquals("development", $this->detector->detect());
    }

    /**
     * Tests a config with this server set to be on the production environment
     */
    public function testThisServerBeingProductionEnvironment()
    {
        $this->detector->registerHost("development", new HostName("8.8.8.8"));
        $this->detector->registerHost("testing", new HostName("8.8.8.2"));
        $this->detector->registerHost("staging", new HostName("8.8.8.4"));
        $this->detector->registerHost("production", new HostName(gethostname()));
        $this->assertEquals("production", $this->detector->detect());
    }

    /**
     * Tests a config with this server set to be on the staging environment
     */
    public function testThisServerBeingStagingEnvironment()
    {
        $this->detector->registerHost("development", new HostName("8.8.8.8"));
        $this->detector->registerHost("testing", new HostName("8.8.8.2"));
        $this->detector->registerHost("staging", new HostName(gethostname()));
        $this->detector->registerHost("production", new HostName("8.8.8.4"));
        $this->assertEquals("staging", $this->detector->detect());
    }

    /**
     * Tests a config with this server set to be on the testing environment
     */
    public function testThisServerBeingTestingEnvironment()
    {
        $this->detector->registerHost("development", new HostName("8.8.8.8"));
        $this->detector->registerHost("testing", new HostName(gethostname()));
        $this->detector->registerHost("staging", new HostName("8.8.8.2"));
        $this->detector->registerHost("production", new HostName("8.8.8.4"));
        $this->assertEquals("testing", $this->detector->detect());
    }

    /**
     * Tests using a regular expression for the host
     */
    public function testUsingRegexForHost()
    {
        // Truncate the last character of the host
        $truncatedHost = substr(gethostname(), 0, -1);
        $this->detector->registerHost("development", new HostName("8.8.8.8"));
        $this->detector->registerHost("testing", new HostName("8.8.8.2"));
        $this->detector->registerHost("staging", new HostRegex("^" . preg_quote($truncatedHost, "/") . ".*$", true));
        $this->detector->registerHost("production", new HostName("8.8.8.4"));
        $this->assertEquals("staging", $this->detector->detect());
    }
} 