<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Environments\Resolvers;

use Opulence\Environments\Environment;
use Opulence\Environments\Hosts\HostName;
use Opulence\Environments\Hosts\HostRegex;

/**
 * Tests the environment resolver class
 */
class EnvironmentResolverTest extends \PHPUnit_Framework_TestCase
{
    /** @var EnvironmentResolver The resolver to use in tests */
    private $resolver = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->resolver = new EnvironmentResolver();
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
        $this->resolver->registerHost("development", $hosts);
        $this->assertEquals("development", $this->resolver->resolve(gethostname()));
    }

    /**
     * Tests registering an empty array
     */
    public function testRegisteringEmptyArray()
    {
        $this->resolver->registerHost("foo", []);
        $this->assertEquals(Environment::PRODUCTION, $this->resolver->resolve(gethostname()));
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
        $this->resolver->registerHost("development", $developmentHosts);
        $stagingHosts = [
            new HostName("8.8.8.2"),
            new HostName(gethostname())
        ];
        $this->resolver->registerHost("staging", $stagingHosts);
        $this->assertEquals("development", $this->resolver->resolve(gethostname()));
    }

    /**
     * Tests registering multiple hosts
     */
    public function testRegisteringMultipleHosts()
    {
        $host1 = new HostName("8.8.8.8");
        $host2 = new HostName(gethostname());
        $host3 = new HostName("8.8.8.2");
        $this->resolver->registerHost("foo", $host1);
        $this->resolver->registerHost("bar", $host2);
        $this->resolver->registerHost("baz", $host3);
        $this->assertEquals("bar", $this->resolver->resolve(gethostname()));
    }

    /**
     * Tests registering a single host
     */
    public function testRegisteringSingleHost()
    {
        $this->resolver->registerHost("development", new HostName(gethostname()));
        $this->assertEquals("development", $this->resolver->resolve(gethostname()));
    }

    /**
     * Tests that the default environment is production
     */
    public function testThatDefaultEnvironmentIsProduction()
    {
        $this->assertEquals(Environment::PRODUCTION, $this->resolver->resolve(gethostname()));
    }

    /**
     * Tests a config with this server set to be on the development environment
     */
    public function testThisServerBeingDevelopmentEnvironment()
    {
        $this->resolver->registerHost("development", new HostName(gethostname()));
        $this->resolver->registerHost("testing", new HostName("8.8.8.2"));
        $this->resolver->registerHost("staging", new HostName("8.8.8.8"));
        $this->resolver->registerHost("production", new HostName("8.8.8.4"));
        $this->assertEquals("development", $this->resolver->resolve(gethostname()));
    }

    /**
     * Tests a config with this server set to be on the production environment
     */
    public function testThisServerBeingProductionEnvironment()
    {
        $this->resolver->registerHost("development", new HostName("8.8.8.8"));
        $this->resolver->registerHost("testing", new HostName("8.8.8.2"));
        $this->resolver->registerHost("staging", new HostName("8.8.8.4"));
        $this->resolver->registerHost("production", new HostName(gethostname()));
        $this->assertEquals("production", $this->resolver->resolve(gethostname()));
    }

    /**
     * Tests a config with this server set to be on the staging environment
     */
    public function testThisServerBeingStagingEnvironment()
    {
        $this->resolver->registerHost("development", new HostName("8.8.8.8"));
        $this->resolver->registerHost("testing", new HostName("8.8.8.2"));
        $this->resolver->registerHost("staging", new HostName(gethostname()));
        $this->resolver->registerHost("production", new HostName("8.8.8.4"));
        $this->assertEquals("staging", $this->resolver->resolve(gethostname()));
    }

    /**
     * Tests a config with this server set to be on the testing environment
     */
    public function testThisServerBeingTestingEnvironment()
    {
        $this->resolver->registerHost("development", new HostName("8.8.8.8"));
        $this->resolver->registerHost("testing", new HostName(gethostname()));
        $this->resolver->registerHost("staging", new HostName("8.8.8.2"));
        $this->resolver->registerHost("production", new HostName("8.8.8.4"));
        $this->assertEquals("testing", $this->resolver->resolve(gethostname()));
    }

    /**
     * Tests using a regular expression for the host
     */
    public function testUsingRegexForHost()
    {
        // Truncate the last character of the host
        $truncatedHost = substr(gethostname(), 0, -1);
        $this->resolver->registerHost("development", new HostName("8.8.8.8"));
        $this->resolver->registerHost("testing", new HostName("8.8.8.2"));
        $this->resolver->registerHost("staging", new HostRegex("^" . preg_quote($truncatedHost, "/") . ".*$", true));
        $this->resolver->registerHost("production", new HostName("8.8.8.4"));
        $this->assertEquals("staging", $this->resolver->resolve(gethostname()));
    }
} 