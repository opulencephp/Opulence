<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the bootstrapper
 */
namespace RDev\Applications\Bootstrappers;
use RDev\Applications\Paths;
use RDev\Applications\Environments\Environment;
use RDev\Sessions\Session;
use RDev\Tests\Applications\Bootstrappers\Mocks\Bootstrapper;

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
        $this->setExpectedException("\\BadMethodCallException");
        $this->bootstrapper->foo("bar");
    }

    /**
     * Tests calling run
     */
    public function testCallingRun()
    {
        $this->bootstrapper->run();
    }
}