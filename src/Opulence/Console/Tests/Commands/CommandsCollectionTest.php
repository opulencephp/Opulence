<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Commands;

use InvalidArgumentException;
use Opulence\Console\Commands\CommandCollection;
use Opulence\Console\Commands\Compilers\Compiler as CommandCompiler;
use Opulence\Console\Responses\Compilers\Compiler;
use Opulence\Console\Responses\Compilers\Lexers\Lexer;
use Opulence\Console\Responses\Compilers\Parsers\Parser;
use Opulence\Console\Responses\SilentResponse;
use Opulence\Console\Tests\Commands\Mocks\HappyHolidayCommand;
use Opulence\Console\Tests\Commands\Mocks\SimpleCommand;
use Opulence\Console\Tests\Responses\Mocks\Response;

/**
 * Tests the command collection class
 */
class CommandsCollectionTest extends \PHPUnit\Framework\TestCase
{
    /** @var CommandCollection The list of commands to test */
    private $collection;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->collection = new CommandCollection(new CommandCompiler());
    }

    /**
     * Tests adding a command
     */
    public function testAdd(): void
    {
        $command = new SimpleCommand('foo', 'The foo command');
        $this->collection->add($command);
        $this->assertSame($command, $this->collection->get('foo'));
    }

    /**
     * Tests adding a command that already exists
     */
    public function testAddingDuplicateNames(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->collection->add(new SimpleCommand('foo', 'The foo command'));
        $this->collection->add(new SimpleCommand('foo', 'The foo command copy'));
    }

    /**
     * Tests calling a command
     */
    public function testCallingCommand(): void
    {
        $this->collection->add(new HappyHolidayCommand());
        $response = new Response(new Compiler(new Lexer(), new Parser()));
        ob_start();
        $this->collection->call('holiday', $response, ['Easter'], ['-y']);
        $this->assertEquals('Happy Easter!', ob_get_clean());
    }

    /**
     * Tests trying to call a non-existent command
     */
    public function testCallingNonExistentCommand(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->collection->call('fake', new SilentResponse(), [], []);
    }

    /**
     * Tests checking if a command exists
     */
    public function testCheckingIfCommandExists(): void
    {
        $this->collection->add(new SimpleCommand('foo', 'The foo command'));
        $this->assertTrue($this->collection->has('foo'));
        $this->assertFalse($this->collection->has('bar'));
    }

    /**
     * Tests getting all commands
     */
    public function testGettingAll(): void
    {
        $fooCommand = new SimpleCommand('foo', 'The foo command');
        $barCommand = new SimpleCommand('bar', 'The bar command');
        $this->collection->add($fooCommand);
        $this->collection->add($barCommand);
        $this->assertEquals([$fooCommand, $barCommand], $this->collection->getAll());
    }

    /**
     * Tests getting a command that does not exists
     */
    public function testGettingCommandThatDoesNotExists(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->collection->get('foo');
    }

    /**
     * Tests overwriting a command that already exists
     */
    public function testOverwritingExistingCommand(): void
    {
        $originalCommand = new SimpleCommand('foo', 'The foo command');
        $overwritingCommand = new SimpleCommand('foo', 'The foo command copy');
        $this->collection->add($originalCommand);
        $this->collection->add($overwritingCommand, true);
        $this->assertSame($overwritingCommand, $this->collection->get('foo'));
    }
}
