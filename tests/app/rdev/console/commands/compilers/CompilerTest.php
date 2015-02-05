<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the command compiler
 */
namespace RDev\Console\Commands\Compilers;
use RDev\Console\Commands;
use RDev\Console\Requests;
use RDev\Tests\Console\Commands\Mocks;

class CompilerTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Compiler The compiler to use in tests */
    private $compiler = null;
    /** @var Commands\Command The command to use in tests */
    private $command = null;
    /** @var Commands\Commands The list of registered commands */
    private $commands = null;
    /** @var Requests\Request The request to use in tests */
    private $request = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->compiler = new Compiler();
        $this->commands = new Commands\Commands($this->compiler);
        $this->command = new Mocks\SimpleCommand("Foo", "The foo command");
        $this->request = new Requests\Request();
    }

    /**
     * Tests compiling an array argument
     */
    public function testCompilingArrayArgument()
    {
        $argument = new Requests\Argument("foo", Requests\ArgumentTypes::IS_ARRAY, "Foo command");
        $this->command->addArgument($argument);
        $this->request->addArgumentValue("bar");
        $this->request->addArgumentValue("baz");
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals(["bar", "baz"], $compiledCommand->getArgumentValue("foo"));
    }

    /**
     * Tests compiling an array argument with an optional argument after it
     */
    public function testCompilingArrayArgumentWitOptionalArgumentAfter()
    {
        $arrayArgument = new Requests\Argument("foo", Requests\ArgumentTypes::IS_ARRAY, "Foo command");
        $optionalArgument = new Requests\Argument("bar", Requests\ArgumentTypes::OPTIONAL, "Bar command", "baz");
        $this->command->addArgument($arrayArgument);
        $this->command->addArgument($optionalArgument);
        $this->request->addArgumentValue("bar");
        $this->request->addArgumentValue("baz");
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals(["bar", "baz"], $compiledCommand->getArgumentValue("foo"));
        $this->assertEquals("baz", $compiledCommand->getArgumentValue("bar"));
    }

    /**
     * Tests compiling an array argument with a required argument after it
     */
    public function testCompilingArrayArgumentWitRequiredArgumentAfter()
    {
        $this->setExpectedException("\\RuntimeException");
        $arrayArgument = new Requests\Argument("foo", Requests\ArgumentTypes::IS_ARRAY, "Foo command");
        $requiredArgument = new Requests\Argument("bar", Requests\ArgumentTypes::REQUIRED, "Bar command");
        $this->command->addArgument($arrayArgument);
        $this->command->addArgument($requiredArgument);
        $this->request->addArgumentValue("bar");
        $this->request->addArgumentValue("baz");
        $this->compiler->compile($this->command, $this->request);
    }

    /**
     * Tests compiling a no value option
     */
    public function testCompilingNoValueOption()
    {
        $option = new Requests\Option("foo", "f", Requests\OptionTypes::NO_VALUE, "Foo command");
        $this->command->addOption($option);
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertNull($compiledCommand->getOptionValue("foo"));
    }

    /**
     * Tests compiling a no value option with a value
     */
    public function testCompilingNoValueOptionWithAValue()
    {
        $this->setExpectedException("\\RuntimeException");
        $option = new Requests\Option("foo", "f", Requests\OptionTypes::NO_VALUE, "Foo command");
        $this->command->addOption($option);
        $this->request->addOptionValue("foo", "bar");
        $this->compiler->compile($this->command, $this->request);
    }

    /**
     * Tests compiling an optional argument
     */
    public function testCompilingOptionalArgument()
    {
        $optionalArgument = new Requests\Argument("foo", Requests\ArgumentTypes::OPTIONAL, "Foo command");
        $this->command->addArgument($optionalArgument);
        $this->request->addArgumentValue("bar");
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals("bar", $compiledCommand->getArgumentValue("foo"));
    }

    /**
     * Tests compiling an optional argument with a default value
     */
    public function testCompilingOptionalArgumentWithDefaultValue()
    {
        $optionalArgument = new Requests\Argument("foo", Requests\ArgumentTypes::OPTIONAL, "Foo command", "baz");
        $this->command->addArgument($optionalArgument);
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals("baz", $compiledCommand->getArgumentValue("foo"));
    }

    /**
     * Tests compiling optional arguments without any values
     */
    public function testCompilingOptionalArgumentsWithoutAnyValues()
    {
        $optionalArgument1 = new Requests\Argument("foo", Requests\ArgumentTypes::OPTIONAL, "Foo command", "fooValue");
        $optionalArgument2 = new Requests\Argument("bar", Requests\ArgumentTypes::OPTIONAL, "Bar command", "barValue");
        $this->command->addArgument($optionalArgument1);
        $this->command->addArgument($optionalArgument2);
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals("fooValue", $compiledCommand->getArgumentValue("foo"));
        $this->assertEquals("barValue", $compiledCommand->getArgumentValue("bar"));
    }

    /**
     * Tests compiling an optional value option with a default value
     */
    public function testCompilingOptionalValueOptionWithDefaultValue()
    {
        $option = new Requests\Option("foo", "f", Requests\OptionTypes::OPTIONAL_VALUE, "Foo command", "bar");
        $this->command->addOption($option);
        $this->request->addOptionValue("foo", null);
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals("bar", $compiledCommand->getOptionValue("foo"));
    }

    /**
     * Tests compiling a required and optional argument
     */
    public function testCompilingRequiredAndOptionalArgument()
    {
        $requiredArgument = new Requests\Argument("foo", Requests\ArgumentTypes::REQUIRED, "Foo command");
        $optionalArgument = new Requests\Argument("bar", Requests\ArgumentTypes::OPTIONAL, "Bar command", "baz");
        $this->command->addArgument($requiredArgument);
        $this->command->addArgument($optionalArgument);
        $this->request->addArgumentValue("bar");
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals("bar", $compiledCommand->getArgumentValue("foo"));
        $this->assertEquals("baz", $compiledCommand->getArgumentValue("bar"));
    }

    /**
     * Tests compiling a required argument
     */
    public function testCompilingRequiredArgument()
    {
        $requiredArgument = new Requests\Argument("foo", Requests\ArgumentTypes::REQUIRED, "Foo command");
        $this->command->addArgument($requiredArgument);
        $this->request->addArgumentValue("bar");
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals("bar", $compiledCommand->getArgumentValue("foo"));
    }

    /**
     * Tests compiling a required argument without a value
     */
    public function testCompilingRequiredArgumentWithoutValue()
    {
        $this->setExpectedException("\\RuntimeException");
        $required = new Requests\Argument("foo", Requests\ArgumentTypes::REQUIRED, "Foo command");
        $this->command->addArgument($required);
        $this->compiler->compile($this->command, $this->request);
    }

    /**
     * Tests compiling required arguments without specifying all values
     */
    public function testCompilingRequiredArgumentsWithoutSpecifyingAllValues()
    {
        $this->setExpectedException("\\RuntimeException");
        $requiredArgument1 = new Requests\Argument("foo", Requests\ArgumentTypes::REQUIRED, "Foo command");
        $requiredArgument2 = new Requests\Argument("bar", Requests\ArgumentTypes::REQUIRED, "Bar command");
        $this->command->addArgument($requiredArgument1);
        $this->command->addArgument($requiredArgument2);
        $this->request->addArgumentValue("bar");
        $this->compiler->compile($this->command, $this->request);
    }

    /**
     * Tests compiling an required value option
     */
    public function testCompilingRequiredValueOption()
    {
        $option = new Requests\Option("foo", "f", Requests\OptionTypes::REQUIRED_VALUE, "Foo command");
        $this->command->addOption($option);
        $this->request->addOptionValue("foo", "bar");
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals("bar", $compiledCommand->getOptionValue("foo"));
    }

    /**
     * Tests compiling a required value option without a value
     */
    public function testCompilingRequiredValueOptionWithoutValue()
    {
        $this->setExpectedException("\\RuntimeException");
        $option = new Requests\Option("foo", "f", Requests\OptionTypes::REQUIRED_VALUE, "Foo command");
        $this->command->addOption($option);
        $this->request->addOptionValue("foo", null);
        $this->compiler->compile($this->command, $this->request);
    }

    /**
     * Tests passing too many arguments
     */
    public function testPassingTooManyArguments()
    {
        $this->setExpectedException("\\RuntimeException");
        $argument = new Requests\Argument("foo", Requests\ArgumentTypes::REQUIRED, "Foo command");
        $this->command->addArgument($argument);
        $this->request->addArgumentValue("bar");
        $this->request->addArgumentValue("baz");
        $this->compiler->compile($this->command, $this->request);
    }

    /**
     * Tests checking that short and long options in a request point to the same option in a command
     */
    public function testThatShortAndLongOptionsPointToSameOption()
    {
        $option = new Requests\Option("foo", "f", Requests\OptionTypes::OPTIONAL_VALUE, "Foo command", "bar");
        $this->command->addOption($option);
        $this->request->addOptionValue("f", null);
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals("bar", $compiledCommand->getOptionValue("foo"));
    }
}