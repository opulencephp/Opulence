<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the console argument
 */
namespace RDev\Console\Requests;

class ArgumentTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Argument The argument to use in tests */
    private $argument = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->argument = new Argument("foo", ArgumentTypes::OPTIONAL, "Foo argument", "bar");
    }

    /**
     * Tests checking whether or not the argument is an array
     */
    public function testCheckingIsArray()
    {
        $arrayArgument = new Argument("foo", ArgumentTypes::IS_ARRAY, "Foo argument");
        $this->assertTrue($arrayArgument->isArray());
    }

    /**
     * Tests checking whether or not the argument is optional
     */
    public function testCheckingIsOptional()
    {
        $requiredArgument = new Argument("foo", ArgumentTypes::REQUIRED, "Foo argument", "bar");
        $optionalArgument = new Argument("foo", ArgumentTypes::OPTIONAL, "Foo argument", "bar");
        $this->assertFalse($requiredArgument->isOptional());
        $this->assertTrue($optionalArgument->isOptional());
    }

    /**
     * Tests checking whether or not the argument is required
     */
    public function testCheckingIsRequired()
    {
        $requiredArgument = new Argument("foo", ArgumentTypes::REQUIRED, "Foo argument", "bar");
        $optionalArgument = new Argument("foo", ArgumentTypes::OPTIONAL, "Foo argument", "bar");
        $this->assertTrue($requiredArgument->isRequired());
        $this->assertFalse($optionalArgument->isRequired());
    }

    /**
     * Tests getting the default value
     */
    public function testGettingDefaultValue()
    {
        $this->assertEquals("bar", $this->argument->getDefaultValue());
    }

    /**
     * Tests getting the description
     */
    public function testGettingDescription()
    {
        $this->assertEquals("Foo argument", $this->argument->getDescription());
    }

    /**
     * Tests getting the name
     */
    public function testGettingName()
    {
        $this->assertEquals("foo", $this->argument->getName());
    }

    /**
     * Tests setting the type to both optional and required
     */
    public function testSettingTypeToOptionalAndRequired()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        new Argument("foo", ArgumentTypes::OPTIONAL | ArgumentTypes::REQUIRED, "Foo argument");
    }
}