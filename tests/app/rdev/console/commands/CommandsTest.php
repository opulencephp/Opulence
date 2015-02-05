<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the commands class
 */
namespace RDev\Console\Commands;
use RDev\Console\Responses;
use RDev\Console\Responses\Compilers as ResponseCompilers;
use RDev\Console\Responses\Compilers\Lexers;
use RDev\Console\Responses\Compilers\Parsers;
use RDev\Tests\Console\Commands\Mocks as CommandMocks;
use RDev\Tests\Console\Responses\Mocks as ResponseMocks;

class CommandsTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Commands The list of commands to test */
    private $commands = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->commands = new Commands(new Compilers\Compiler());
    }

    /**
     * Tests adding a command
     */
    public function testAdd()
    {
        $command = new CommandMocks\SimpleCommand("foo", "The foo command");
        $this->commands->add($command);
        $this->assertSame($command, $this->commands->get("foo"));
    }

    /**
     * Tests adding a command that already exists
     */
    public function testAddingDuplicateNames()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->commands->add(new CommandMocks\SimpleCommand("foo", "The foo command"));
        $this->commands->add(new CommandMocks\SimpleCommand("foo", "The foo command copy"));
    }

    /**
     * Tests calling a command
     */
    public function testCallingCommand()
    {
        $this->commands->add(new CommandMocks\HappyHolidayCommand());
        $response = new ResponseMocks\Response(
            new ResponseCompilers\Compiler(new Lexers\Lexer(), new Parsers\Parser())
        );
        ob_start();
        $this->commands->call("holiday", ["Easter"], ["-y"], $response);
        $this->assertEquals("Happy Easter!", ob_get_clean());
    }

    /**
     * Tests trying to call a non-existent command
     */
    public function testCallingNonExistentCommand()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->commands->call("fake", [], [], new Responses\Silent());
    }

    /**
     * Tests checking if a command exists
     */
    public function testCheckingIfCommandExists()
    {
        $this->commands->add(new CommandMocks\SimpleCommand("foo", "The foo command"));
        $this->assertTrue($this->commands->has("foo"));
        $this->assertFalse($this->commands->has("bar"));
    }

    /**
     * Tests getting all commands
     */
    public function testGettingAll()
    {
        $fooCommand = new CommandMocks\SimpleCommand("foo", "The foo command");
        $barCommand = new CommandMocks\SimpleCommand("bar", "The bar command");
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