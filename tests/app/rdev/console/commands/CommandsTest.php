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
        $command = new Mocks\Command("foo", "The foo command");
        $this->commands->add($command);
        $this->assertSame($command, $this->commands->get("foo"));
    }

    /**
     * Tests adding a command that already exists
     */
    public function testAddingDuplicateNames()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->commands->add(new Mocks\Command("foo", "The foo command"));
        $this->commands->add(new Mocks\Command("foo", "The foo command copy"));
    }

    /**
     * Tests checking if a command exists
     */
    public function testCheckingIfCommandExists()
    {
        $this->commands->add(new Mocks\Command("foo", "The foo command"));
        $this->assertTrue($this->commands->has("foo"));
        $this->assertFalse($this->commands->has("bar"));
    }

    /**
     * Tests getting all commands
     */
    public function testGettingAll()
    {
        $fooCommand = new Mocks\Command("foo", "The foo command");
        $barCommand = new Mocks\Command("bar", "The bar command");
        $this->commands->add($fooCommand);
        $this->commands->add($barCommand);
        $this->assertEquals([$fooCommand, $barCommand], $this->commands->getAll());
    }

    /**
     * Tests getting a command that does not exists
     */
    public function testGettingCommandThatDoesNotExists()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->commands->get("foo");
    }
}