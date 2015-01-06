<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the console command
 */
namespace RDev\Console\Commands;
use RDev\Console\Requests;
use RDev\Tests\Console\Commands\Mocks;

class CommandTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Mocks\Command The command to use in tests */
    private $command = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->command = new Mocks\Command("foo", "The foo command");
    }

    /**
     * Tests adding an argument
     */
    public function testAddingArgument()
    {
        $this->assertEquals([], $this->command->getArguments());
        $argument = new Requests\Argument("foo", Requests\ArgumentTypes::OPTIONAL, "bar", null);
        $this->command->addArgument($argument);
        $this->assertSame($argument, $this->command->getArgument("foo"));
        $this->assertSame([$argument], $this->command->getArguments());
    }

    /**
     * Tests adding an option
     */
    public function testAddingOption()
    {
        $this->assertEquals([], $this->command->getOptions());
        $option = new Requests\Option("foo", Requests\OptionTypes::OPTIONAL_VALUE, "bar", null);
        $this->command->addOption($option);
        $this->assertSame($option, $this->command->getOption("foo"));
        $this->assertSame([$option], $this->command->getOptions());
    }

    /**
     * Tests getting the description
     */
    public function testGettingDescription()
    {
        $this->assertEquals("The foo command", $this->command->getDescription());
    }

    /**
     * Tests getting the name
     */
    public function testGettingName()
    {
        $this->assertEquals("foo", $this->command->getName());
    }

    /**
     * Tests getting a non-existent argument
     */
    public function testGettingNonExistentArgument()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->command->getArgumentValue("fake");
    }

    /**
     * Tests getting a non-existent argument value
     */
    public function testGettingNonExistentArgumentValue()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->command->getArgument("fake");
    }

    /**
     * Tests getting a non-existent option value
     */
    public function testGettingNonExistentOptionValue()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->command->getOptionValue("fake");
    }

    /**
     * Tests getting a non-existent option
     */
    public function testGettingNonExistentOption()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->command->getOption("fake");
    }

    /**
     * Tests setting an argument value
     */
    public function testSettingArgumentValue()
    {
        $this->command->setArgumentValue("foo", "bar");
        $this->assertEquals("bar", $this->command->getArgumentValue("foo"));
    }

    /**
     * Tests setting an option value
     */
    public function testSettingOptionValue()
    {
        $this->command->setOptionValue("foo", "bar");
        $this->assertEquals("bar", $this->command->getOptionValue("foo"));
    }

    /**
     * Tests that the command configuration is called in the constructor
     */
    public function testThatCommandConfigurationIsCalledInConstructor()
    {
        $this->assertEquals("argumentSetInMock", $this->command->getArgumentSetInMock()->getName());
    }
}