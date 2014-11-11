<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the environment detector class
 */
namespace RDev\Applications;

class EnvironmentDetectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that the default environment is production
     */
    public function testThatDefaultEnvironmentIsProduction()
    {
        $detector = new EnvironmentDetector(new Configs\EnvironmentConfig([]));
        $this->assertEquals(Environment::PRODUCTION, $detector->detect());
    }

    /**
     * Tests a config with this server set to be on the development environment
     */
    public function testThisServerBeingDevelopmentEnvironment()
    {
        $config = new Configs\EnvironmentConfig([
            "names" => [
                "development" => gethostname(),
                "testing" => "8.8.8.2",
                "staging" => "8.8.8.8",
                "production" => "8.8.8.4"
            ]
        ]);
        $detector = new EnvironmentDetector($config);
        $this->assertEquals(Environment::DEVELOPMENT, $detector->detect());
    }

    /**
     * Tests a config with this server set to be on the production environment
     */
    public function testThisServerBeingProductionEnvironment()
    {
        $config = new Configs\EnvironmentConfig([
            "names" => [
                "development" => "8.8.8.8",
                "testing" => "8.8.8.2",
                "staging" => "8.8.8.4",
                "production" => gethostname()
            ]
        ]);
        $detector = new EnvironmentDetector($config);
        $this->assertEquals(Environment::PRODUCTION, $detector->detect());
    }

    /**
     * Tests a config with this server set to be on the staging environment
     */
    public function testThisServerBeingStagingEnvironment()
    {
        $config = new Configs\EnvironmentConfig([
            "names" => [
                "development" => "8.8.8.8",
                "testing" => "8.8.8.2",
                "staging" => gethostname(),
                "production" => "8.8.8.4"
            ]
        ]);
        $detector = new EnvironmentDetector($config);
        $this->assertEquals(Environment::STAGING, $detector->detect());
    }

    /**
     * Tests a config with this server set to be on the testing environment
     */
    public function testThisServerBeingTestingEnvironment()
    {
        $config = new Configs\EnvironmentConfig([
            "names" => [
                "development" => "8.8.8.8",
                "testing" => gethostname(),
                "staging" => "8.8.8.2",
                "production" => "8.8.8.4"
            ]
        ]);
        $detector = new EnvironmentDetector($config);
        $this->assertEquals(Environment::TESTING, $detector->detect());
    }

    /**
     * Tests using a regular expression for the host
     */
    public function testUsingRegexForHost()
    {
        // Truncate the last character of the host
        $truncatedHost = substr(gethostname(), 0, -1);
        $config = new Configs\EnvironmentConfig([
            "names" => [
                "development" => "8.8.8.8",
                "testing" => "8.8.8.2",
                "staging" => [
                    ["type" => "regex", "value" => "/^" . preg_quote($truncatedHost, "/") . ".*$/"]
                ],
                "production" => "8.8.8.4"
            ]
        ]);
        $detector = new EnvironmentDetector($config);
        $this->assertEquals(Environment::STAGING, $detector->detect());
    }

    /**
     * Tests a config with a callback
     */
    public function testWithCallback()
    {
        $config = new Configs\EnvironmentConfig([
            "names" => [
                function ()
                {
                    return "staging";
                }
            ]
        ]);
        $detector = new EnvironmentDetector($config);
        $this->assertEquals(Environment::STAGING, $detector->detect());
    }
} 