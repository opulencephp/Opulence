<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the console request
 */
namespace RDev\Console\Requests;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var Request The request to use in tests */
    private $request = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->request = new Request();
    }

    /**
     * Tests getting all arguments
     */
    public function testGettingAllArguments()
    {
        $this->request->addArgumentValue("foo");
        $this->request->addArgumentValue("bar");
        $this->assertEquals(["foo", "bar"], $this->request->getArgumentValues());
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
     * Tests getting the command name
     */
    public function testGettingCommandName()
    {
        $this->request->setCommandName("foo");
        $this->assertEquals("foo", $this->request->getCommandName());
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