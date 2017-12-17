<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Console\Tests;

use Opulence\Console\Commands\CommandCollection;
use Opulence\Console\Commands\Compilers\Compiler as CommandCompiler;
use Opulence\Console\Kernel;
use Opulence\Console\Requests\Parsers\StringParser;
use Opulence\Console\Requests\Tokenizers\StringTokenizer;
use Opulence\Console\Responses\Compilers\Compiler as ResponseCompiler;
use Opulence\Console\Responses\Compilers\Lexers\Lexer;
use Opulence\Console\Responses\Compilers\Parsers\Parser;
use Opulence\Console\StatusCodes;
use Opulence\Console\Tests\Commands\Mocks\HappyHolidayCommand;
use Opulence\Console\Tests\Commands\Mocks\SimpleCommand;
use Opulence\Console\Tests\Responses\Mocks\Response;

/**
 * Tests the console kernel
 */
class KernelTest extends \PHPUnit\Framework\TestCase
{
    /** @var CommandCompiler The command compiler */
    private $compiler = null;
    /** @var CommandCollection The list of commands */
    private $commands = null;
    /** @var StringParser The request parser */
    private $parser = null;
    /** @var Response The response to use in tests */
    private $response = null;
    /** @var Kernel The kernel to use in tests */
    private $kernel = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->compiler = new CommandCompiler();
        $this->commands = new CommandCollection($this->compiler);
        $this->commands->add(new SimpleCommand('mockcommand', 'Mocks a command'));
        $this->commands->add(new HappyHolidayCommand($this->commands));
        $this->parser = new StringParser(new StringTokenizer());
        $this->response = new Response(new ResponseCompiler(new Lexer(), new Parser()));
        $this->kernel = new Kernel(
            $this->parser,
            $this->compiler,
            $this->commands,
            '0.0.0'
        );
    }

    /**
     * Tests handling an exception
     */
    public function testHandlingException() : void
    {
        ob_start();
        $status = $this->kernel->handle("unclosed quote '", $this->response);
        ob_end_clean();
        $this->assertEquals(StatusCodes::FATAL, $status);
    }

    /**
     * Tests handling a help command
     */
    public function testHandlingHelpCommand() : void
    {
        // Try with command name
        ob_start();
        $status = $this->kernel->handle('help holiday', $this->response);
        ob_get_clean();
        $this->assertEquals(StatusCodes::OK, $status);

        // Try with command name with no argument
        ob_start();
        $status = $this->kernel->handle('help', $this->response);
        ob_get_clean();
        $this->assertEquals(StatusCodes::OK, $status);

        // Try with short name
        ob_start();
        $status = $this->kernel->handle('holiday -h', $this->response);
        ob_get_clean();
        $this->assertEquals(StatusCodes::OK, $status);

        // Try with long name
        ob_start();
        $status = $this->kernel->handle('holiday --help', $this->response);
        ob_get_clean();
        $this->assertEquals(StatusCodes::OK, $status);
    }

    /**
     * Tests handling help command with non-existent command
     */
    public function testHandlingHelpCommandWithNonExistentCommand() : void
    {
        ob_start();
        $status = $this->kernel->handle('help fake', $this->response);
        ob_end_clean();
        $this->assertEquals(StatusCodes::ERROR, $status);
    }

    /**
     * Tests handling command with arguments and options
     */
    public function testHandlingHolidayCommand() : void
    {
        // Test with short option
        ob_start();
        $status = $this->kernel->handle('holiday birthday -y', $this->response);
        $this->assertEquals('Happy birthday!', ob_get_clean());
        $this->assertEquals(StatusCodes::OK, $status);

        // Test with long option
        ob_start();
        $status = $this->kernel->handle('holiday Easter --yell=no', $this->response);
        $this->assertEquals('Happy Easter', ob_get_clean());
        $this->assertEquals(StatusCodes::OK, $status);
    }

    /**
     * Tests handling in a missing command
     */
    public function testHandlingMissingCommand() : void
    {
        ob_start();
        $status = $this->kernel->handle('fake', $this->response);
        ob_get_clean();
        $this->assertEquals(StatusCodes::OK, $status);
    }

    /**
     * Tests handling in a simple command
     */
    public function testHandlingSimpleCommand() : void
    {
        ob_start();
        $status = $this->kernel->handle('mockcommand', $this->response);
        $this->assertEquals('foo', ob_get_clean());
        $this->assertEquals(StatusCodes::OK, $status);
    }

    /**
     * Tests handling a version command
     */
    public function testHandlingVersionCommand() : void
    {
        // Try with short name
        ob_start();
        $status = $this->kernel->handle('-v', $this->response);
        ob_get_clean();
        $this->assertEquals(StatusCodes::OK, $status);

        // Try with long name
        ob_start();
        $status = $this->kernel->handle('--version', $this->response);
        ob_get_clean();
        $this->assertEquals(StatusCodes::OK, $status);
    }
}
