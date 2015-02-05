<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the console command
 */
namespace RDev\Console\Commands;
use RDev\Console\Requests;
use RDev\Console\Responses;
use RDev\Console\Responses\Compilers as ResponseCompilers;
use RDev\Console\Responses\Compilers\Lexers;
use RDev\Console\Responses\Compilers\Parsers;
use RDev\Tests\Console\Responses\Mocks as ResponseMocks;
use RDev\Tests\Console\Commands\Mocks as CommandMocks;

class CommandTest extends \PHPUnit_Framework_TestCase 
{
    /** @var CommandMocks\SimpleCommand The command to use in tests */
    private $command = null;
    /** @var Commands The list of registered commands */
    private $commands = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->commands = new Commands(new Compilers\Compiler());
        $this->commands->add(new CommandMocks\HappyHolidayCommand($this->commands));
        $this->command = new CommandMocks\SimpleCommand(
            "foo", "The foo command", "Bob Loblaw's Law Blog no habla Espanol"
        );
    }

    /**
     * Tests adding an argument
     */
    public function testAddingArgument()
    {
        $this->assertEquals([], $this->command->getArguments());
        $argument = new Requests\Argument("foo", Requests\ArgumentTypes::OPTIONAL, "bar", null);
        $returnValue = $this->command->addArgument($argument);
        $this->assertSame($returnValue, $this->command);
        $this->assertSame($argument, $this->command->getArgument("foo"));
        $this->assertSame([$argument], $this->command->getArguments());
    }

    /**
     * Tests adding an option
     */
    public function testAddingOption()
    {
        $option = new Requests\Option("foo", "f", Requests\OptionTypes::OPTIONAL_VALUE, "bar", null);
        $returnValue = $this->command->addOption($option);
        $this->assertSame($returnValue, $this->command);
        $this->assertSame($option, $this->command->getOption("foo"));
    }

    /**
     * Tests checking if an argument has a value
     */
    public function testCheckingIfArgumentHasValue()
    {
        $noValueArgument = new Requests\Argument("novalue", Requests\ArgumentTypes::OPTIONAL, "No value");
        $hasValueArgument = new Requests\Argument("hasvalue", Requests\ArgumentTypes::REQUIRED, "Has value");
        $this->command->addArgument($noValueArgument)
            ->addArgument($hasValueArgument)
            ->setArgumentValue("hasvalue", "foo");
        $this->assertFalse($this->command->argumentValueIsSet("novalue"));
        $this->assertTrue($this->command->argumentValueIsSet("hasvalue"));
    }

    /**
     * Tests checking if a set option is set
     */
    public function testCheckingIfSetOptionIsSet()
    {
        $option = new Requests\Option("foo", "f", Requests\OptionTypes::REQUIRED_VALUE, "Foo command");
        $this->command->addOption($option);
        $this->command->setOptionValue("foo", "bar");
        $this->assertTrue($this->command->optionIsSet("foo"));
    }

    /**
     * Tests checking if a set option without a value is set
     */
    public function testCheckingIfSetOptionWithoutValueIsSet()
    {
        $option = new Requests\Option("foo", "f", Requests\OptionTypes::OPTIONAL_VALUE, "Foo command");
        $this->command->addOption($option);
        $this->command->setOptionValue("foo", null);
        $this->assertTrue($this->command->optionIsSet("foo"));
    }

    /**
     * Tests checking if an unset option is set
     */
    public function testCheckingIfUnsetOptionIsSet()
    {
        $this->assertFalse($this->command->optionIsSet("fake"));
    }

    /**
     * Tests creating a command that does not construct its parent
     */
    public function testCreatingCommandThatDoesNotConstructParent()
    {
        $this->setExpectedException("\\RuntimeException");
        $command = new CommandMocks\CommandThatDoesNotCallParentConstructor();
        $command->execute(new ResponseMocks\Response(
            new ResponseCompilers\Compiler(new Lexers\Lexer(), new Parsers\Parser())
        ));
    }

    /**
     * Tests getting the description
     */
    public function testGettingDescription()
    {
        $this->assertEquals("The foo command", $this->command->getDescription());
    }

    /**
     * Tests Getting the help text
     */
    public function testGettingHelpText()
    {
        $this->assertEquals("Bob Loblaw's Law Blog no habla Espanol", $this->command->getHelpText());
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
     * Tests getting a non-existent option
     */
    public function testGettingNonExistentOption()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->command->getOption("fake");
    }

    /**
     * Tests getting the value of a non-existent option
     */
    public function testGettingValueOfNonExistentOption()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->command->getOptionValue("fake");
    }

    /**
     * Tests getting the value of an option with a default value
     */
    public function testGettingValueOfOptionWithDefaultValue()
    {
        $option = new Requests\Option("foo", "f", Requests\OptionTypes::OPTIONAL_VALUE, "Foo command", "bar");
        $this->command->addOption($option);
        $this->assertNull($this->command->getOptionValue("foo"));
    }

    /**
     * Tests not setting the command name in the constructor
     */
    public function testNotSettingNameInConstructor()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        new CommandMocks\NamelessCommand(new Commands(new Compilers\Compiler()));
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
        $option = new Requests\Option("foo", "f", Requests\OptionTypes::OPTIONAL_VALUE, "Foo command", "bar");
        $this->command->addOption($option);
        $this->command->setOptionValue("foo", "bar");
        $this->assertEquals("bar", $this->command->getOptionValue("foo"));
    }
}