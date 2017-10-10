<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Console\Tests\Commands;

use InvalidArgumentException;
use Opulence\Console\Commands\CommandCollection;
use Opulence\Console\Commands\Compilers\Compiler as CommandCompiler;
use Opulence\Console\Requests\Argument;
use Opulence\Console\Requests\ArgumentTypes;
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Responses\Compilers\Compiler as ResponseCompiler;
use Opulence\Console\Responses\Compilers\Lexers\Lexer;
use Opulence\Console\Responses\Compilers\Parsers\Parser;
use Opulence\Console\Tests\Commands\Mocks\CommandThatDoesNotCallParentConstructor;
use Opulence\Console\Tests\Commands\Mocks\HappyHolidayCommand;
use Opulence\Console\Tests\Commands\Mocks\NamelessCommand;
use Opulence\Console\Tests\Commands\Mocks\SimpleCommand;
use Opulence\Console\Tests\Responses\Mocks\Response;
use RuntimeException;

/**
 * Tests the console command
 */
class CommandTest extends \PHPUnit\Framework\TestCase
{
    /** @var SimpleCommand The command to use in tests */
    private $command = null;
    /** @var CommandCollection The list of registered commands */
    private $commandCollection = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->commandCollection = new CommandCollection(new CommandCompiler());
        $this->commandCollection->add(new HappyHolidayCommand($this->commandCollection));
        $this->command = new SimpleCommand('foo', 'The foo command', "Bob Loblaw's Law Blog no habla Espanol");
    }

    /**
     * Tests adding an argument
     */
    public function testAddingArgument()
    {
        $this->assertEquals([], $this->command->getArguments());
        $argument = new Argument('foo', ArgumentTypes::OPTIONAL, 'bar', null);
        $returnValue = $this->command->addArgument($argument);
        $this->assertSame($returnValue, $this->command);
        $this->assertSame($argument, $this->command->getArgument('foo'));
        $this->assertSame([$argument], $this->command->getArguments());
    }

    /**
     * Tests adding an option
     */
    public function testAddingOption()
    {
        $option = new Option('foo', 'f', OptionTypes::OPTIONAL_VALUE, 'bar', null);
        $returnValue = $this->command->addOption($option);
        $this->assertSame($returnValue, $this->command);
        $this->assertSame($option, $this->command->getOption('foo'));
    }

    /**
     * Tests checking if an argument has a value
     */
    public function testCheckingIfArgumentHasValue()
    {
        $noValueArgument = new Argument('novalue', ArgumentTypes::OPTIONAL, 'No value');
        $hasValueArgument = new Argument('hasvalue', ArgumentTypes::REQUIRED, 'Has value');
        $this->command->addArgument($noValueArgument)
            ->addArgument($hasValueArgument)
            ->setArgumentValue('hasvalue', 'foo');
        $this->assertFalse($this->command->argumentValueIsSet('novalue'));
        $this->assertTrue($this->command->argumentValueIsSet('hasvalue'));
    }

    /**
     * Tests checking if a set option is set
     */
    public function testCheckingIfSetOptionIsSet()
    {
        $option = new Option('foo', 'f', OptionTypes::REQUIRED_VALUE, 'Foo command');
        $this->command->addOption($option);
        $this->command->setOptionValue('foo', 'bar');
        $this->assertTrue($this->command->optionIsSet('foo'));
    }

    /**
     * Tests checking if a set option without a value is set
     */
    public function testCheckingIfSetOptionWithoutValueIsSet()
    {
        $option = new Option('foo', 'f', OptionTypes::OPTIONAL_VALUE, 'Foo command');
        $this->command->addOption($option);
        $this->command->setOptionValue('foo', null);
        $this->assertTrue($this->command->optionIsSet('foo'));
    }

    /**
     * Tests checking if an unset option is set
     */
    public function testCheckingIfUnsetOptionIsSet()
    {
        $this->assertFalse($this->command->optionIsSet('fake'));
    }

    /**
     * Tests creating a command that does not construct its parent
     */
    public function testCreatingCommandThatDoesNotConstructParent()
    {
        $this->expectException(RuntimeException::class);
        $command = new CommandThatDoesNotCallParentConstructor();
        $command->execute(new Response(new ResponseCompiler(new Lexer(), new Parser())));
    }

    /**
     * Tests getting the description
     */
    public function testGettingDescription()
    {
        $this->assertEquals('The foo command', $this->command->getDescription());
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
        $this->assertEquals('foo', $this->command->getName());
    }

    /**
     * Tests getting a non-existent argument
     */
    public function testGettingNonExistentArgument()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->command->getArgumentValue('fake');
    }

    /**
     * Tests getting a non-existent argument value
     */
    public function testGettingNonExistentArgumentValue()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->command->getArgument('fake');
    }

    /**
     * Tests getting a non-existent option
     */
    public function testGettingNonExistentOption()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->command->getOption('fake');
    }

    /**
     * Tests getting the value of a non-existent option
     */
    public function testGettingValueOfNonExistentOption()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->command->getOptionValue('fake');
    }

    /**
     * Tests getting the value of an option with a default value
     */
    public function testGettingValueOfOptionWithDefaultValue()
    {
        $option = new Option('foo', 'f', OptionTypes::OPTIONAL_VALUE, 'Foo command', 'bar');
        $this->command->addOption($option);
        $this->assertNull($this->command->getOptionValue('foo'));
    }

    /**
     * Tests not setting the command name in the constructor
     */
    public function testNotSettingNameInConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new NamelessCommand(new CommandCollection(new CommandCompiler()));
    }

    /**
     * Tests setting an argument value
     */
    public function testSettingArgumentValue()
    {
        $this->command->setArgumentValue('foo', 'bar');
        $this->assertEquals('bar', $this->command->getArgumentValue('foo'));
    }

    /**
     * Tests setting an option value
     */
    public function testSettingOptionValue()
    {
        $option = new Option('foo', 'f', OptionTypes::OPTIONAL_VALUE, 'Foo command', 'bar');
        $this->command->addOption($option);
        $this->command->setOptionValue('foo', 'bar');
        $this->assertEquals('bar', $this->command->getOptionValue('foo'));
    }
}
