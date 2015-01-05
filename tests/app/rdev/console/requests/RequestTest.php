<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the console input
 */
namespace RDev\Console\Input;
use RDev\Tests\Console\Input\Mocks;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var Mocks\Input The input to use in tests */
    private $input = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->input = new Mocks\Input();
    }

    /**
     * Tests getting all arguments
     */
    public function testGettingAllArguments()
    {
        $this->input->setArgument("foo", "bar");
        $this->input->setArgument("baz", "blah");
        $this->assertEquals(["foo" => "bar", "baz" => "blah"], $this->input->getArguments());
    }

    /**
     * Tests getting all options
     */
    public function testGettingAllOptions()
    {
        $this->input->setOption("foo", "bar");
        $this->input->setOption("baz", "blah");
        $this->assertEquals(["foo" => "bar", "baz" => "blah"], $this->input->getOptions());
    }

    /**
     * Tests getting an argument
     */
    public function testGettingArgument()
    {
        $this->input->setArgument("foo", "bar");
        $this->assertEquals("bar", $this->input->getArgument("foo"));
    }

    /**
     * Tests getting a non-existent argument
     */
    public function testGettingNonExistentArgument()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->input->getArgument("foo");
    }

    /**
     * Tests getting a non-existent option
     */
    public function testGettingNonExistentOption()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->input->getOption("foo");
    }

    /**
     * Tests getting an option
     */
    public function testGettingOption()
    {
        $this->input->setOption("foo", "bar");
        $this->assertEquals("bar", $this->input->getOption("foo"));
    }
}