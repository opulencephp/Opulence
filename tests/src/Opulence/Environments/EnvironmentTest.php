<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Environments;

/**
 * Tests the environment
 */
class EnvironmentTest extends \PHPUnit\Framework\TestCase
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
     * Tests default name is production
     */
    public function testDefaultNameIsProduction()
    {
        $environment = new Environment();
        $this->assertEquals(Environment::PRODUCTION, $environment->getName());
    }

    /**
     * Tests getting the name
     */
    public function testGettingName()
    {
        $this->assertEquals("foo", $this->environment->getName());
    }

    /**
     * Tests getting a non-existent variable
     */
    public function testGettingNonExistentVariable()
    {
        $this->assertEquals("bar", $this->environment->getVar("foo", "bar"));
    }

    /**
     * Tests getting a variable
     */
    public function testGettingVariable()
    {
        $this->environment->setVar("baz", "blah");
        $this->assertEquals("blah", getenv("baz"));
        $this->assertEquals("blah", $_ENV["baz"]);
        $this->assertEquals("blah", $_SERVER["baz"]);
    }

    /**
     * Tests checking if the application is running in a console
     */
    public function testIsRunningInConsole()
    {
        $isRunningInConsole = $this->environment->isRunningInConsole();

        if (php_sapi_name() == "cli") {
            $this->assertTrue($isRunningInConsole);
        } else {
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
        $this->environment->setVar("foo", "bar");
        $this->assertEquals("bar", getenv("foo"));
        $this->assertEquals("bar", $_ENV["foo"]);
        $this->assertEquals("bar", $_SERVER["foo"]);
    }

    /**
     * Tests setting a variable in environment global array()
     */
    public function testSettingVariableInEnvironmentGlobalArray()
    {
        $_ENV["bar"] = "baz";
        $this->assertEquals("baz", $this->environment->getVar("bar"));
    }

    /**
     * Tests setting a variable in putenv()
     */
    public function testSettingVariableInPutenv()
    {
        putenv("bar=baz");
        $this->assertEquals("baz", $this->environment->getVar("bar"));
    }

    /**
     * Tests setting a variable in server global array()
     */
    public function testSettingVariableInServerGlobalArray()
    {
        $_SERVER["bar"] = "baz";
        $this->assertEquals("baz", $this->environment->getVar("bar"));
    }
}