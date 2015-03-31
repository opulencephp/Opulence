<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the command formatter
 */
namespace RDev\Console\Responses\Formatters;
use RDev\Console\Commands\CommandCollection;
use RDev\Console\Commands\Compilers\Compiler;
use RDev\Console\Requests\Argument;
use RDev\Console\Requests\ArgumentTypes;
use RDev\Console\Requests\Option;
use RDev\Console\Requests\OptionTypes;
use RDev\Tests\Console\Commands\Mocks\SimpleCommand;

class CommandFormatterTest extends \PHPUnit_Framework_TestCase
{
    /** @var CommandFormatter The formatter to use in tests */
    private $formatter = null;
    /** @var CommandCollection The list of registered commands */
    private $commandCollection = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->formatter = new CommandFormatter();
        $this->commandCollection = new CommandCollection(new Compiler());
    }

    /**
     * Tests formatting a command with mix of arguments
     */
    public function testFormattingCommandWithMixOfArguments()
    {
        $command = new SimpleCommand("foo", "Foo command");
        $command->addArgument(new Argument(
            "bar",
            ArgumentTypes::REQUIRED,
            "Bar argument"
        ));
        $command->addArgument(new Argument(
            "baz",
            ArgumentTypes::OPTIONAL,
            "Baz argument"
        ));
        $command->addArgument(new Argument(
            "blah",
            ArgumentTypes::IS_ARRAY,
            "Blah argument"
        ));
        $this->assertEquals("foo [--help|-h] bar [baz] blah1...blahN", $this->formatter->format($command));
    }

    /**
     * Tests formatting a command with multiple arguments
     */
    public function testFormattingCommandWithMultipleArguments()
    {
        $command = new SimpleCommand("foo", "Foo command");
        $command->addArgument(new Argument(
            "bar",
            ArgumentTypes::REQUIRED,
            "Bar argument"
        ));
        $command->addArgument(new Argument(
            "baz",
            ArgumentTypes::REQUIRED,
            "Baz argument"
        ));
        $this->assertEquals("foo [--help|-h] bar baz", $this->formatter->format($command));
    }

    /**
     * Tests formatting a command with no arguments or options
     */
    public function testFormattingCommandWithNoArgumentsOrOptions()
    {
        $command = new SimpleCommand("foo", "Foo command");
        $this->assertEquals("foo [--help|-h]", $this->formatter->format($command));
    }

    /**
     * Tests formatting a command with one argument
     */
    public function testFormattingCommandWithOneArgument()
    {
        $command = new SimpleCommand("foo", "Foo command");
        $command->addArgument(new Argument(
            "bar",
            ArgumentTypes::REQUIRED,
            "Bar argument"
        ));
        $this->assertEquals("foo [--help|-h] bar", $this->formatter->format($command));
    }

    /**
     * Tests formatting a command with one option with a default value
     */
    public function testFormattingCommandWithOneOptionWithDefaultValue()
    {
        $command = new SimpleCommand("foo", "Foo command");
        $command->addOption(new Option(
            "bar",
            "b",
            OptionTypes::OPTIONAL_VALUE,
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
        $command = new SimpleCommand("foo", "Foo command");
        $command->addOption(new Option(
            "bar",
            null,
            OptionTypes::OPTIONAL_VALUE,
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
        $command = new SimpleCommand("foo", "Foo command");
        $command->addOption(new Option(
            "bar",
            null,
            OptionTypes::NO_VALUE,
            "Bar option"
        ));
        $this->assertEquals("foo [--help|-h] [--bar]", $this->formatter->format($command));
    }

    /**
     * Tests formatting a command with one optional argument
     */
    public function testFormattingCommandWithOneOptionalArgument()
    {
        $command = new SimpleCommand("foo", "Foo command");
        $command->addArgument(new Argument(
            "bar",
            ArgumentTypes::OPTIONAL,
            "Bar argument"
        ));
        $this->assertEquals("foo [--help|-h] [bar]", $this->formatter->format($command));
    }

    /**
     * Tests formatting a command with an optional array argument
     */
    public function testFormattingCommandWithOptionalArrayArgument()
    {
        $command = new SimpleCommand("foo", "Foo command");
        $command->addArgument(new Argument(
            "blah",
            ArgumentTypes::IS_ARRAY | ArgumentTypes::OPTIONAL,
            "Blah argument"
        ));
        $this->assertEquals("foo [--help|-h] [blah1]...[blahN]", $this->formatter->format($command));
    }
}