<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the environment fetcher class
 */
namespace RDev\Models\Applications;

class EnvironmentFetcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that the default environment is production
     */
    public function testThatDefaultEnvironmentIsProduction()
    {
        $fetcher = new EnvironmentFetcher();
        $this->assertEquals(Application::ENV_PRODUCTION, $fetcher->getEnvironment([]));
    }

    /**
     * Tests a config with this server set to be on the development environment
     */
    public function testThisServerBeingDevelopmentEnvironment()
    {
        $config = [
            "development" => gethostname(),
            "testing" => "8.8.8.2",
            "staging" => "8.8.8.8",
            "production" => "8.8.8.4"
        ];
        $fetcher = new EnvironmentFetcher();
        $this->assertEquals(Application::ENV_DEVELOPMENT, $fetcher->getEnvironment($config));
    }

    /**
     * Tests a config with this server set to be on the production environment
     */
    public function testThisServerBeingProductionEnvironment()
    {
        $config = [
            "development" => "8.8.8.8",
            "testing" => "8.8.8.2",
            "staging" => "8.8.8.4",
            "production" => gethostname()
        ];
        $fetcher = new EnvironmentFetcher();
        $this->assertEquals(Application::ENV_PRODUCTION, $fetcher->getEnvironment($config));
    }

    /**
     * Tests a config with this server set to be on the staging environment
     */
    public function testThisServerBeingStagingEnvironment()
    {
        $config = [
            "development" => "8.8.8.8",
            "testing" => "8.8.8.2",
            "staging" => gethostname(),
            "production" => "8.8.8.4"
        ];
        $fetcher = new EnvironmentFetcher();
        $this->assertEquals(Application::ENV_STAGING, $fetcher->getEnvironment($config));
    }

    /**
     * Tests a config with this server set to be on the testing environment
     */
    public function testThisServerBeingTestingEnvironment()
    {
        $config = [
            "development" => "8.8.8.8",
            "testing" => gethostname(),
            "staging" => "8.8.8.2",
            "production" => "8.8.8.4"
        ];
        $fetcher = new EnvironmentFetcher();
        $this->assertEquals(Application::ENV_TESTING, $fetcher->getEnvironment($config));
    }

    /**
     * Tests using a regular expression for the host
     */
    public function testUsingRegexForHost()
    {
        // Use a wildcard as the last character of the host
        $hostWithWildcard = substr(gethostname(), 0, -1) . ".";
        $config = [
            "development" => "8.8.8.8",
            "testing" => "8.8.8.2",
            "staging" => $hostWithWildcard,
            "production" => "8.8.8.4"
        ];
        $fetcher = new EnvironmentFetcher();
        $this->assertEquals(Application::ENV_STAGING, $fetcher->getEnvironment($config));
    }

    /**
     * Tests a config with a callback
     */
    public function testWithCallback()
    {
        $config = [
            function ()
            {
                return "staging";
            }
        ];
        $fetcher = new EnvironmentFetcher();
        $this->assertEquals(Application::ENV_STAGING, $fetcher->getEnvironment($config));
    }
} 