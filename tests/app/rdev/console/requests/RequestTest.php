<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the console request
 */
namespace RDev\Console\Requests;
use RDev\Tests\Console\Requests\Mocks;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var Mocks\Request The request to use in tests */
    private $request = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->request = new Mocks\Request();
    }

    /**
     * Tests getting all arguments
     */
    public function testGettingAllArguments()
    {
        $this->request->setArgument("foo", "bar");
        $this->request->setArgument("baz", "blah");
        $this->assertEquals(["foo" => "bar", "baz" => "blah"], $this->request->getArguments());
    }

    /**
     * Tests getting all options
     */
    public function testGettingAllOptions()
    {
        $this->request->setOption("foo", "bar");
        $this->request->setOption("baz", "blah");
        $this->assertEquals(["foo" => "bar", "baz" => "blah"], $this->request->getOptions());
    }

    /**
     * Tests getting an argument
     */
    public function testGettingArgument()
    {
        $this->request->setArgument("foo", "bar");
        $this->assertEquals("bar", $this->request->getArgument("foo"));
    }

    /**
     * Tests getting a non-existent argument
     */
    public function testGettingNonExistentArgument()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->request->getArgument("foo");
    }

    /**
     * Tests getting a non-existent option
     */
    public function testGettingNonExistentOption()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->request->getOption("foo");
    }

    /**
     * Tests getting an option
     */
    public function testGettingOption()
    {
        $this->request->setOption("foo", "bar");
        $this->assertEquals("bar", $this->request->getOption("foo"));
    }
}