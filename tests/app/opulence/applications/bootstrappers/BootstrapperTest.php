<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the bootstrapper
 */
namespace Opulence\Applications\Bootstrappers;

use BadMethodCallException;
use Opulence\Applications\Paths;
use Opulence\Applications\Environments\Environment;
use Opulence\Sessions\Session;
use Opulence\Tests\Applications\Bootstrappers\Mocks\Bootstrapper;

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