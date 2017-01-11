<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Commands\Compilers;

use Opulence\Console\Commands\Command;
use Opulence\Console\Commands\CommandCollection;
use Opulence\Console\Requests\Argument;
use Opulence\Console\Requests\ArgumentTypes;
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Requests\Request;
use Opulence\Tests\Console\Commands\Mocks\SimpleCommand;
use RuntimeException;

/**
 * Tests the command compiler
 */
class CompilerTest extends \PHPUnit\Framework\TestCase
{
    /** @var Compiler The compiler to use in tests */
    private $compiler = null;
    /** @var Command The command to use in tests */
    private $command = null;
    /** @var CommandCollection The list of registered commands */
    private $commandCollection = null;
    /** @var Request The request to use in tests */
    private $request = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->compiler = new Compiler();
        $this->commandCollection = new CommandCollection($this->compiler);
        $this->command = new SimpleCommand('Foo', 'The foo command');
        $this->request = new Request();
    }

    /**
     * Tests compiling an array argument
     */
    public function testCompilingArrayArgument()
    {
        $argument = new Argument('foo', ArgumentTypes::IS_ARRAY, 'Foo command');
        $this->command->addArgument($argument);
        $this->request->addArgumentValue('bar');
        $this->request->addArgumentValue('baz');
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals(['bar', 'baz'], $compiledCommand->getArgumentValue('foo'));
    }

    /**
     * Tests compiling an array argument with an optional argument after it
     */
    public function testCompilingArrayArgumentWitOptionalArgumentAfter()
    {
        $arrayArgument = new Argument('foo', ArgumentTypes::IS_ARRAY, 'Foo command');
        $optionalArgument = new Argument('bar', ArgumentTypes::OPTIONAL, 'Bar command', 'baz');
        $this->command->addArgument($arrayArgument);
        $this->command->addArgument($optionalArgument);
        $this->request->addArgumentValue('bar');
        $this->request->addArgumentValue('baz');
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals(['bar', 'baz'], $compiledCommand->getArgumentValue('foo'));
        $this->assertEquals('baz', $compiledCommand->getArgumentValue('bar'));
    }

    /**
     * Tests compiling an array argument with a required argument after it
     */
    public function testCompilingArrayArgumentWitRequiredArgumentAfter()
    {
        $this->expectException(RuntimeException::class);
        $arrayArgument = new Argument('foo', ArgumentTypes::IS_ARRAY, 'Foo command');
        $requiredArgument = new Argument('bar', ArgumentTypes::REQUIRED, 'Bar command');
        $this->command->addArgument($arrayArgument);
        $this->command->addArgument($requiredArgument);
        $this->request->addArgumentValue('bar');
        $this->request->addArgumentValue('baz');
        $this->compiler->compile($this->command, $this->request);
    }

    /**
     * Tests compiling a no value option
     */
    public function testCompilingNoValueOption()
    {
        $option = new Option('foo', 'f', OptionTypes::NO_VALUE, 'Foo command');
        $this->command->addOption($option);
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertNull($compiledCommand->getOptionValue('foo'));
    }

    /**
     * Tests compiling a no value option with a value
     */
    public function testCompilingNoValueOptionWithAValue()
    {
        $this->expectException(RuntimeException::class);
        $option = new Option('foo', 'f', OptionTypes::NO_VALUE, 'Foo command');
        $this->command->addOption($option);
        $this->request->addOptionValue('foo', 'bar');
        $this->compiler->compile($this->command, $this->request);
    }

    /**
     * Tests compiling an option with a null short name still compiles
     */
    public function testCompilingOptionWithNullShortNameStillCompiles()
    {
        $option = new Option('foo', null, OptionTypes::REQUIRED_VALUE, 'Foo command');
        $this->command->addOption($option);
        $this->request->addOptionValue('foo', 'bar');
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals('bar', $compiledCommand->getOptionValue('foo'));
    }

    /**
     * Tests compiling an optional argument
     */
    public function testCompilingOptionalArgument()
    {
        $optionalArgument = new Argument('foo', ArgumentTypes::OPTIONAL, 'Foo command');
        $this->command->addArgument($optionalArgument);
        $this->request->addArgumentValue('bar');
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals('bar', $compiledCommand->getArgumentValue('foo'));
    }

    /**
     * Tests compiling an optional argument with a default value
     */
    public function testCompilingOptionalArgumentWithDefaultValue()
    {
        $optionalArgument = new Argument('foo', ArgumentTypes::OPTIONAL, 'Foo command', 'baz');
        $this->command->addArgument($optionalArgument);
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals('baz', $compiledCommand->getArgumentValue('foo'));
    }

    /**
     * Tests compiling optional arguments without any values
     */
    public function testCompilingOptionalArgumentsWithoutAnyValues()
    {
        $optionalArgument1 = new Argument('foo', ArgumentTypes::OPTIONAL, 'Foo command', 'fooValue');
        $optionalArgument2 = new Argument('bar', ArgumentTypes::OPTIONAL, 'Bar command', 'barValue');
        $this->command->addArgument($optionalArgument1);
        $this->command->addArgument($optionalArgument2);
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals('fooValue', $compiledCommand->getArgumentValue('foo'));
        $this->assertEquals('barValue', $compiledCommand->getArgumentValue('bar'));
    }

    /**
     * Tests compiling an optional value option with a default value
     */
    public function testCompilingOptionalValueOptionWithDefaultValue()
    {
        $option = new Option('foo', 'f', OptionTypes::OPTIONAL_VALUE, 'Foo command', 'bar');
        $this->command->addOption($option);
        $this->request->addOptionValue('foo', null);
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals('bar', $compiledCommand->getOptionValue('foo'));
    }

    /**
     * Tests compiling a required and optional argument
     */
    public function testCompilingRequiredAndOptionalArgument()
    {
        $requiredArgument = new Argument('foo', ArgumentTypes::REQUIRED, 'Foo command');
        $optionalArgument = new Argument('bar', ArgumentTypes::OPTIONAL, 'Bar command', 'baz');
        $this->command->addArgument($requiredArgument);
        $this->command->addArgument($optionalArgument);
        $this->request->addArgumentValue('bar');
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals('bar', $compiledCommand->getArgumentValue('foo'));
        $this->assertEquals('baz', $compiledCommand->getArgumentValue('bar'));
    }

    /**
     * Tests compiling a required argument
     */
    public function testCompilingRequiredArgument()
    {
        $requiredArgument = new Argument('foo', ArgumentTypes::REQUIRED, 'Foo command');
        $this->command->addArgument($requiredArgument);
        $this->request->addArgumentValue('bar');
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals('bar', $compiledCommand->getArgumentValue('foo'));
    }

    /**
     * Tests compiling a required argument without a value
     */
    public function testCompilingRequiredArgumentWithoutValue()
    {
        $this->expectException(RuntimeException::class);
        $required = new Argument('foo', ArgumentTypes::REQUIRED, 'Foo command');
        $this->command->addArgument($required);
        $this->compiler->compile($this->command, $this->request);
    }

    /**
     * Tests compiling required arguments without specifying all values
     */
    public function testCompilingRequiredArgumentsWithoutSpecifyingAllValues()
    {
        $this->expectException(RuntimeException::class);
        $requiredArgument1 = new Argument('foo', ArgumentTypes::REQUIRED, 'Foo command');
        $requiredArgument2 = new Argument('bar', ArgumentTypes::REQUIRED, 'Bar command');
        $this->command->addArgument($requiredArgument1);
        $this->command->addArgument($requiredArgument2);
        $this->request->addArgumentValue('bar');
        $this->compiler->compile($this->command, $this->request);
    }

    /**
     * Tests compiling an required value option
     */
    public function testCompilingRequiredValueOption()
    {
        $option = new Option('foo', 'f', OptionTypes::REQUIRED_VALUE, 'Foo command');
        $this->command->addOption($option);
        $this->request->addOptionValue('foo', 'bar');
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals('bar', $compiledCommand->getOptionValue('foo'));
    }

    /**
     * Tests compiling a required value option without a value
     */
    public function testCompilingRequiredValueOptionWithoutValue()
    {
        $this->expectException(RuntimeException::class);
        $option = new Option('foo', 'f', OptionTypes::REQUIRED_VALUE, 'Foo command');
        $this->command->addOption($option);
        $this->request->addOptionValue('foo', null);
        $this->compiler->compile($this->command, $this->request);
    }

    /**
     * Tests passing too many arguments
     */
    public function testPassingTooManyArguments()
    {
        $this->expectException(RuntimeException::class);
        $argument = new Argument('foo', ArgumentTypes::REQUIRED, 'Foo command');
        $this->command->addArgument($argument);
        $this->request->addArgumentValue('bar');
        $this->request->addArgumentValue('baz');
        $this->compiler->compile($this->command, $this->request);
    }

    /**
     * Tests checking that short and long options in a request point to the same option in a command
     */
    public function testThatShortAndLongOptionsPointToSameOption()
    {
        $option = new Option('foo', 'f', OptionTypes::OPTIONAL_VALUE, 'Foo command', 'bar');
        $this->command->addOption($option);
        $this->request->addOptionValue('f', null);
        $compiledCommand = $this->compiler->compile($this->command, $this->request);
        $this->assertEquals('bar', $compiledCommand->getOptionValue('foo'));
    }
}
