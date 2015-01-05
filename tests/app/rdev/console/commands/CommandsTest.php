<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the commands class
 */
namespace RDev\Console\Commands;
use RDev\Tests\Console\Commands\Mocks;

class CommandsTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Commands The list of commands to test */
    private $commands = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->commands = new Commands();
    }

    /**
     * Tests adding a command
     */
    public function testAdd()
    {
        $command = new Mocks\Command();
        $this->commands->add("foo", $command, "The foo command");
        $this->assertSame($command, $this->commands->get("foo"));
    }

    /**
     * Tests adding a command that already exists
     */
    public function testAddingDuplicateNames()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->commands->add("foo", new Mocks\Command(), "The foo command");
        $this->commands->add("foo", new Mocks\Command(), "The foo command");
    }

    /**
     * Tests checking if a command exists
     */
    public function testCheckingIfCommandExists()
    {
        $this->commands->add("foo", new Mocks\Command(), "The foo command");
        $this->assertTrue($this->commands->has("foo"));
        $this->assertFalse($this->commands->has("bar"));
    }

    /**
     * Tests getting all commands
     */
    public function testGettingAll()
    {
        $fooCommand = new Mocks\Command();
        $barCommand = new Mocks\Command();
        $this->commands->add("foo", $fooCommand, "The foo command");
        $this->commands->add("bar", $barCommand, "The bar command");
        $expectedOutput = [
            ["name" => "foo", "command" => $fooCommand, "description" => "The foo command"],
            ["name" => "bar", "command" => $barCommand, "description" => "The bar command"]
        ];
        $this->assertEquals($expectedOutput, $this->commands->getAll());
    }

    /**
     * Tests getting a command that does not exists
     */
    public function testGettingCommandThatDoesNotExists()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->commands->get("foo");
    }

    /**
     * Tests getting a command's description
     */
    public function testGettingDescription()
    {
        $this->commands->add("foo", new Mocks\Command(), "The foo command");
        $this->assertSame("The foo command", $this->commands->getDescription("foo"));
        $this->assertEmpty($this->commands->getDescription("bar"));
    }
}