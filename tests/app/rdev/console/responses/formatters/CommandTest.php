<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the command formatter
 */
namespace RDev\Console\Responses\Formatters;
use RDev\Console\Commands;
use RDev\Console\Commands\Compilers;
use RDev\Console\Requests;
use RDev\Tests\Console\Commands\Mocks;

class CommandTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Command The formatter to use in tests */
    private $formatter = null;
    /** @var Commands\Commands The list of registered commands */
    private $commands = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->formatter = new Command();
        $this->commands = new Commands\Commands(new Compilers\Compiler());
    }

    /**
     * Tests formatting a command with mix of arguments
     */
    public function testFormattingCommandWithMixOfArguments()
    {
        $command = new Mocks\SimpleCommand("foo", "Foo command");
        $command->addArgument(new Requests\Argument(
            "bar",
            Requests\ArgumentTypes::REQUIRED,
            "Bar argument"
        ));
        $command->addArgument(new Requests\Argument(
            "baz",
            Requests\ArgumentTypes::OPTIONAL,
            "Baz argument"
        ));
        $command->addArgument(new Requests\Argument(
            "blah",
            Requests\ArgumentTypes::IS_ARRAY,
            "Blah argument"
        ));
        $this->assertEquals("foo [--help|-h] bar [baz] blah1...blahN", $this->formatter->format($command));
    }

    /**
     * Tests formatting a command with multiple arguments
     */
    public function testFormattingCommandWithMultipleArguments()
    {
        $command = new Mocks\SimpleCommand("foo", "Foo command");
        $command->addArgument(new Requests\Argument(
            "bar",
            Requests\ArgumentTypes::REQUIRED,
            "Bar argument"
        ));
        $command->addArgument(new Requests\Argument(
            "baz",
            Requests\ArgumentTypes::REQUIRED,
            "Baz argument"
        ));
        $this->assertEquals("foo [--help|-h] bar baz", $this->formatter->format($command));
    }

    /**
     * Tests formatting a command with no arguments or options
     */
    public function testFormattingCommandWithNoArgumentsOrOptions()
    {
        $command = new Mocks\SimpleCommand("foo", "Foo command");
        $this->assertEquals("foo [--help|-h]", $this->formatter->format($command));
    }

    /**
     * Tests formatting a command with one argument
     */
    public function testFormattingCommandWithOneArgument()
    {
        $command = new Mocks\SimpleCommand("foo", "Foo command");
        $command->addArgument(new Requests\Argument(
            "bar",
            Requests\ArgumentTypes::REQUIRED,
            "Bar argument"
        ));
        $this->assertEquals("foo [--help|-h] bar", $this->formatter->format($command));
    }

    /**
     * Tests formatting a command with one option with a default value
     */
    public function testFormattingCommandWithOneOptionWithDefaultValue()
    {
        $command = new Mocks\SimpleCommand("foo", "Foo command");
        $command->addOption(new Requests\Option(
            "bar",
            "b",
            Requests\OptionTypes::OPTIONAL_VALUE,
            "Bar option",
            "yes"
        ));
        $this->assertEquals("foo [--help|-h] [--bar=yes|-b]", $this->formatter->format($command));
    }

    /**
     * Tests formatting a command with one option with default value but no short name
     */
    public function testFormattingCommandWithOneOptionWithDefaultValueButNoShortName()
    {
        $command = new Mocks\SimpleCommand("foo", "Foo command");
        $command->addOption(new Requests\Option(
            "bar",
            null,
            Requests\OptionTypes::OPTIONAL_VALUE,
            "Bar option",
            "yes"
        ));
        $this->assertEquals("foo [--help|-h] [--bar=yes]", $this->formatter->format($command));
    }

    /**
     * Tests formatting a command with one option with no short name
     */
    public function testFormattingCommandWithOneOptionWithoutShortName()
    {
        $command = new Mocks\SimpleCommand("foo", "Foo command");
        $command->addOption(new Requests\Option(
            "bar",
            null,
            Requests\OptionTypes::NO_VALUE,
            "Bar option"
        ));
        $this->assertEquals("foo [--help|-h] [--bar]", $this->formatter->format($command));
    }

    /**
     * Tests formatting a command with one optional argument
     */
    public function testFormattingCommandWithOneOptionalArgument()
    {
        $command = new Mocks\SimpleCommand("foo", "Foo command");
        $command->addArgument(new Requests\Argument(
            "bar",
            Requests\ArgumentTypes::OPTIONAL,
            "Bar argument"
        ));
        $this->assertEquals("foo [--help|-h] [bar]", $this->formatter->format($command));
    }

    /**
     * Tests formatting a command with an optional array argument
     */
    public function testFormattingCommandWithOptionalArrayArgument()
    {
        $command = new Mocks\SimpleCommand("foo", "Foo command");
        $command->addArgument(new Requests\Argument(
            "blah",
            Requests\ArgumentTypes::IS_ARRAY | Requests\ArgumentTypes::OPTIONAL,
            "Blah argument"
        ));
        $this->assertEquals("foo [--help|-h] [blah1]...[blahN]", $this->formatter->format($command));
    }
}