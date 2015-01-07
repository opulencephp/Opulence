<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the console kernel
 */
namespace RDev\Console\Kernels;
use Monolog;
use RDev\Console\Commands;
use RDev\Console\Commands\Compilers;
use RDev\Console\Requests\Parsers;
use RDev\Tests\Applications\Mocks as ApplicationMocks;
use RDev\Tests\Console\Commands\Mocks as CommandMocks;
use RDev\Tests\Console\Responses\Mocks as ResponseMocks;

class KernelTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Compilers\Compiler The command compiler */
    private $compiler = null;
    /** @var Commands\Commands The list of commands */
    private $commands = null;
    /** @var Parsers\String The request parser */
    private $parser = null;
    /** @var ResponseMocks\Response The response to use in tests */
    private $response = null;
    /** @var Kernel The kernel to use in tests */
    private $kernel = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $logger = new Monolog\Logger("application");
        $logger->pushHandler(new ApplicationMocks\MonologHandler());
        $this->compiler = new Compilers\Compiler();
        $this->commands = new Commands\Commands();
        $this->commands->add(new CommandMocks\SimpleCommand("mockcommand", "Mocks a command"));
        $this->commands->add(new CommandMocks\HappyHolidayCommand());
        $this->parser = new Parsers\String();
        $this->response = new ResponseMocks\Response();
        $this->kernel = new Kernel($this->compiler, $this->commands, $logger);
    }

    /**
     * Tests handling an exception
     */
    public function testHandlingException()
    {
        ob_start();
        $status = $this->kernel->handle($this->parser, "unclosed quote '", $this->response);
        $response = ob_get_clean();
        $this->assertEquals(StatusCodes::ERROR, $status);
        $this->assertEquals("Error: ", substr($response, 0, 7));
    }

    /**
     * Tests handling a help option
     */
    public function testHandlingHelpCommand()
    {
        // Try with command name
        ob_start();
        $status = $this->kernel->handle($this->parser, "help holiday", $this->response);
        $response = ob_get_clean();
        error_log($response);
        $this->assertEquals(StatusCodes::OK, $status);

        // Try with short name
        ob_start();
        $status = $this->kernel->handle($this->parser, "holiday -h", $this->response);
        $response = ob_get_clean();
        $this->assertEquals(StatusCodes::OK, $status);

        // Try with long name
        ob_start();
        $status = $this->kernel->handle($this->parser, "holiday --help", $this->response);
        $response = ob_get_clean();
        $this->assertEquals(StatusCodes::OK, $status);
    }

    /**
     * Tests handling help command with non-existent command
     */
    public function testHandlingHelpCommandWithNonExistentCommand()
    {
        ob_start();
        $status = $this->kernel->handle($this->parser, "help fake", $this->response);
        $response = ob_get_clean();
        $this->assertEquals(StatusCodes::ERROR, $status);
        $this->assertEquals("Error: ", substr($response, 0, 7));
    }

    /**
     * Tests handling command with arguments and options
     */
    public function testHandlingHolidayCommand()
    {
        // Test with short option
        ob_start();
        $status = $this->kernel->handle($this->parser, "holiday birthday -y", $this->response);
        $this->assertEquals("Happy birthday!", ob_get_clean());
        $this->assertEquals(StatusCodes::OK, $status);

        // Test with long option
        ob_start();
        $status = $this->kernel->handle($this->parser, "holiday Easter --yell=no", $this->response);
        $this->assertEquals("Happy Easter", ob_get_clean());
        $this->assertEquals(StatusCodes::OK, $status);
    }

    /**
     * Tests handling in a missing command
     */
    public function testHandlingMissingCommand()
    {
        ob_start();
        $status = $this->kernel->handle($this->parser, "fake", $this->response);
        $response = ob_get_clean();
        $this->assertEquals("About RDev Console", substr($response, 0, 18));
        $this->assertEquals(StatusCodes::OK, $status);
    }

    /**
     * Tests handling in a simple command
     */
    public function testHandlingSimpleCommand()
    {
        ob_start();
        $status = $this->kernel->handle($this->parser, "mockcommand", $this->response);
        $this->assertEquals("foo", ob_get_clean());
        $this->assertEquals(StatusCodes::OK, $status);
    }
}