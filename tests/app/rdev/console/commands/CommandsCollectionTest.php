<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the command collection class
 */
namespace RDev\Console\Commands;
use InvalidArgumentException;
use RDev\Console\Commands\Compilers\Compiler as CommandCompiler;
use RDev\Console\Responses\SilentResponse;
use RDev\Console\Responses\Compilers\Compiler;
use RDev\Console\Responses\Compilers\Lexers\Lexer;
use RDev\Console\Responses\Compilers\Parsers\Parser;
use RDev\Tests\Console\Commands\Mocks\HappyHolidayCommand;
use RDev\Tests\Console\Commands\Mocks\SimpleCommand;
use RDev\Tests\Console\Responses\Mocks\Response;

class CommandsCollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var CommandCollection The list of commands to test */
    private $collection = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->collection = new CommandCollection(new CommandCompiler());
    }

    /**
     * Tests adding a command
     */
    public function testAdd()
    {
        $command = new SimpleCommand("foo", "The foo command");
        $this->collection->add($command);
        $this->assertSame($command, $this->collection->get("foo"));
    }

    /**
     * Tests adding a command that already exists
     */
    public function testAddingDuplicateNames()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->collection->add(new SimpleCommand("foo", "The foo command"));
        $this->collection->add(new SimpleCommand("foo", "The foo command copy"));
    }

    /**
     * Tests calling a command
     */
    public function testCallingCommand()
    {
        $this->collection->add(new HappyHolidayCommand());
        $response = new Response(new Compiler(new Lexer(), new Parser()));
        ob_start();
        $this->collection->call("holiday", $response, ["Easter"], ["-y"]);
        $this->assertEquals("Happy Easter!", ob_get_clean());
    }

    /**
     * Tests trying to call a non-existent command
     */
    public function testCallingNonExistentCommand()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->collection->call("fake", new SilentResponse(), [], []);
    }

    /**
     * Tests checking if a command exists
     */
    public function testCheckingIfCommandExists()
    {
        $this->collection->add(new SimpleCommand("foo", "The foo command"));
        $this->assertTrue($this->collection->has("foo"));
        $this->assertFalse($this->collection->has("bar"));
    }

    /**
     * Tests getting all commands
     */
    public function testGettingAll()
    {
        $fooCommand = new SimpleCommand("foo", "The foo command");
        $barCommand = new SimpleCommand("bar", "The bar command");
        $this->collection->add($fooCommand);
        $this->collection->add($barCommand);
        $this->assertEquals([$fooCommand, $barCommand], $this->collection->getAll());
    }

    /**
     * Tests getting a command that does not exists
     */
    public function testGettingCommandThatDoesNotExists()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->collection->get("foo");
    }

    /**
     * Tests overwriting a command that already exists
     */
    public function testOverwritingExistingCommand()
    {
        $originalCommand = new SimpleCommand("foo", "The foo command");
        $overwritingCommand = new SimpleCommand("foo", "The foo command copy");
        $this->collection->add($originalCommand);
        $this->collection->add($overwritingCommand, true);
        $this->assertSame($overwritingCommand, $this->collection->get("foo"));
    }
}