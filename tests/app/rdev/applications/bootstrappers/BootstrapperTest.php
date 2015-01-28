<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the bootstrapper
 */
namespace RDev\Applications\Bootstrappers;
use Monolog;
use RDev\Applications;
use RDev\Applications\Environments;
use RDev\Sessions;
use RDev\Tests\Applications\Bootstrappers\Mocks;

class BootstrapperTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Mocks\Bootstrapper The bootstrapper to use in tests */
    private $bootstrapper = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->bootstrapper = new Mocks\Bootstrapper(
            new Applications\Paths([]),
            new Environments\Environment("testing"),
            new Sessions\Session()
        );
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