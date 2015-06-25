<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the environment detector class
 */
namespace RDev\Applications\Environments;
use RDev\Applications\Environments\Hosts\Host;
use RDev\Applications\Environments\Hosts\HostRegistry;

class EnvironmentDetectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var EnvironmentDetector The detector to use in tests */
    private $detector = null;
    /** @var HostRegistry|\PHPUnit_Framework_MockObject_MockObject The registry to use in tests */
    private $registry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->detector = new EnvironmentDetector();
        $this->registry = $this->getMock(HostRegistry::class);
    }

    /**
     * Tests that the default environment is production
     */
    public function testThatDefaultEnvironmentIsProduction()
    {
        $this->registry->expects($this->any())->method("getHosts")->willReturn([]);
        $this->assertEquals(Environment::PRODUCTION, $this->detector->detect($this->registry));
    }

    /**
     * Tests a config with this server set to be on the development environment
     */
    public function testThisServerBeingDevelopmentEnvironment()
    {
        $hosts = [
            "development" => [new Host(gethostname(), false)],
            "testing" => [new Host("8.8.8.2", false)],
            "staging" => [new Host("8.8.8.8", false)],
            "production" => [new Host("8.8.8.4", false)]
        ];
        $this->registry->expects($this->any())->method("getHosts")->willReturn($hosts);
        $this->assertEquals(Environment::DEVELOPMENT, $this->detector->detect($this->registry));
    }

    /**
     * Tests a config with this server set to be on the production environment
     */
    public function testThisServerBeingProductionEnvironment()
    {
        $hosts = [
            "development" => [new Host("8.8.8", false)],
            "testing" => [new Host("8.8.8.2", false)],
            "staging" => [new Host("8.8.8.4", false)],
            "production" => [new Host(gethostname(), false)]
        ];
        $this->registry->expects($this->any())->method("getHosts")->willReturn($hosts);
        $this->assertEquals(Environment::PRODUCTION, $this->detector->detect($this->registry));
    }

    /**
     * Tests a config with this server set to be on the staging environment
     */
    public function testThisServerBeingStagingEnvironment()
    {
        $hosts = [
            "development" => [new Host("8.8.8", false)],
            "testing" => [new Host("8.8.8.2", false)],
            "staging" => [new Host(gethostname(), false)],
            "production" => [new Host("8.8.8.4", false)]
        ];
        $this->registry->expects($this->any())->method("getHosts")->willReturn($hosts);
        $this->assertEquals(Environment::STAGING, $this->detector->detect($this->registry));
    }

    /**
     * Tests a config with this server set to be on the testing environment
     */
    public function testThisServerBeingTestingEnvironment()
    {
        $hosts = [
            "development" => [new Host("8.8.8", false)],
            "testing" => [new Host(gethostname(), false)],
            "staging" => [new Host("8.8.8.2", false)],
            "production" => [new Host("8.8.8.4", false)]
        ];
        $this->registry->expects($this->any())->method("getHosts")->willReturn($hosts);
        $this->assertEquals(Environment::TESTING, $this->detector->detect($this->registry));
    }

    /**
     * Tests using a regular expression for the host
     */
    public function testUsingRegexForHost()
    {
        // Truncate the last character of the host
        $truncatedHost = substr(gethostname(), 0, -1);
        $hosts = [
            "development" => [new Host("8.8.8", false)],
            "testing" => [new Host("8.8.8.2", false)],
            "staging" => [new Host("/^" . preg_quote($truncatedHost, "/") . ".*$/", true)],
            "production" => [new Host("8.8.8.4", false)]
        ];
        $this->registry->expects($this->any())->method("getHosts")->willReturn($hosts);
        $this->assertEquals(Environment::STAGING, $this->detector->detect($this->registry));
    }
} 