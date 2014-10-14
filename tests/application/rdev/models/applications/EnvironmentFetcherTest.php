<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the environment fetcher class
 */
namespace RDev\Models\Applications;

use RDev\Models\Applications\Configs\EnvironmentConfig;

class EnvironmentFetcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that the default environment is production
     */
    public function testThatDefaultEnvironmentIsProduction()
    {
        $fetcher = new EnvironmentFetcher();
        $this->assertEquals(Application::ENV_PRODUCTION, $fetcher->getEnvironment(new EnvironmentConfig([])));
    }

    /**
     * Tests a config with this server set to be on the development environment
     */
    public function testThisServerBeingDevelopmentEnvironment()
    {
        $config = new EnvironmentConfig([
            "development" => gethostname(),
            "testing" => "8.8.8.2",
            "staging" => "8.8.8.8",
            "production" => "8.8.8.4"
        ]);
        $fetcher = new EnvironmentFetcher();
        $this->assertEquals(Application::ENV_DEVELOPMENT, $fetcher->getEnvironment($config));
    }

    /**
     * Tests a config with this server set to be on the production environment
     */
    public function testThisServerBeingProductionEnvironment()
    {
        $config = new EnvironmentConfig([
            "development" => "8.8.8.8",
            "testing" => "8.8.8.2",
            "staging" => "8.8.8.4",
            "production" => gethostname()
        ]);
        $fetcher = new EnvironmentFetcher();
        $this->assertEquals(Application::ENV_PRODUCTION, $fetcher->getEnvironment($config));
    }

    /**
     * Tests a config with this server set to be on the staging environment
     */
    public function testThisServerBeingStagingEnvironment()
    {
        $config = new EnvironmentConfig([
            "development" => "8.8.8.8",
            "testing" => "8.8.8.2",
            "staging" => gethostname(),
            "production" => "8.8.8.4"
        ]);
        $fetcher = new EnvironmentFetcher();
        $this->assertEquals(Application::ENV_STAGING, $fetcher->getEnvironment($config));
    }

    /**
     * Tests a config with this server set to be on the testing environment
     */
    public function testThisServerBeingTestingEnvironment()
    {
        $config = new EnvironmentConfig([
            "development" => "8.8.8.8",
            "testing" => gethostname(),
            "staging" => "8.8.8.2",
            "production" => "8.8.8.4"
        ]);
        $fetcher = new EnvironmentFetcher();
        $this->assertEquals(Application::ENV_TESTING, $fetcher->getEnvironment($config));
    }

    /**
     * Tests using a regular expression for the host
     */
    public function testUsingRegexForHost()
    {
        // Truncate the last character of the host
        $truncatedHost = substr(gethostname(), 0, -1);
        $config = new EnvironmentConfig([
            "development" => "8.8.8.8",
            "testing" => "8.8.8.2",
            "staging" => [
                ["type" => "regex", "value" => "/^" . preg_quote($truncatedHost, "/") . ".*$/"]
            ],
            "production" => "8.8.8.4"
        ]);
        $fetcher = new EnvironmentFetcher();
        $this->assertEquals(Application::ENV_STAGING, $fetcher->getEnvironment($config));
    }

    /**
     * Tests a config with a callback
     */
    public function testWithCallback()
    {
        $config = new EnvironmentConfig([
            function ()
            {
                return "staging";
            }
        ]);
        $fetcher = new EnvironmentFetcher();
        $this->assertEquals(Application::ENV_STAGING, $fetcher->getEnvironment($config));
    }
} 