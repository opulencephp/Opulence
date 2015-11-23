<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Bootstrappers;

use BadMethodCallException;
use Opulence\Environments\Environment;
use Opulence\Sessions\Session;
use Opulence\Tests\Bootstrappers\Mocks\Bootstrapper;

/**
 * Tests the bootstrapper
 */
class BootstrapperTest extends \PHPUnit_Framework_TestCase
{
    /** @var Bootstrapper The bootstrapper to use in tests */
    private $bootstrapper = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->bootstrapper = new Bootstrapper(new Paths([]), new Environment("testing"), new Session());
    }

    /**
     * Tests calling a bad method
     */
    public function testCallingBadMethod()
    {
        $this->setExpectedException(BadMethodCallException::class);
        $this->bootstrapper->foo("bar");
    }

    /**
     * Tests calling initialize
     */
    public function testCallingInitialize()
    {
        $this->bootstrapper->initialize();
    }

    /**
     * Tests calling run
     */
    public function testCallingRun()
    {
        $this->bootstrapper->run();
    }

    /**
     * Tests calling shutdown
     */
    public function testCallingShutdown()
    {
        $this->bootstrapper->shutdown();
    }
}