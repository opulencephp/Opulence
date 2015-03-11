<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the console application tester
 */
namespace RDev\Framework\Tests;
use RDev\Console\Kernels;
use RDev\Console\Prompts;
use RDev\Console\Responses\Formatters;
use RDev\Tests\Console\Commands\Mocks as CommandMocks;
use RDev\Tests\Framework\Tests\Mocks as TestMocks;

class ConsoleApplicationTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /** @var TestMocks\ConsoleApplicationTestCase The console application to use in tests */
    private $application = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->application = new TestMocks\ConsoleApplicationTestCase();
        $this->application->setUp();
        $prompt = new Prompts\Prompt(new Formatters\Padding());
        $this->application->getCommands()->add(new CommandMocks\SimpleCommand("simple", "Simple command"));
        $this->application->getCommands()->add(new CommandMocks\StyledCommand());
        $this->application->getCommands()->add(new CommandMocks\HappyHolidayCommand());
        $this->application->getCommands()->add(new CommandMocks\StatusCodeCommand());
        $this->application->getCommands()->add(new CommandMocks\SinglePromptCommand($prompt));
        $this->application->getCommands()->add(new CommandMocks\MultiplePromptsCommand($prompt));
    }

    /**
     * Tests asserting that the status code is an error
     */
    public function testAssertStatusCodeIsError()
    {
        $this->application->call("simple", [], ["--code=" . Kernels\StatusCodes::ERROR]);
        $this->application->assertStatusCodeIsOK();
    }

    /**
     * Tests asserting that the status code is fatal
     */
    public function testAssertStatusCodeIsFatal()
    {
        $this->application->call("simple", [], ["--code=" . Kernels\StatusCodes::FATAL]);
        $this->application->assertStatusCodeIsOK();
    }

    /**
     * Tests asserting that the status code is OK
     */
    public function testAssertStatusCodeIsOK()
    {
        $this->application->call("simple", [], ["--code=" . Kernels\StatusCodes::OK]);
        $this->application->assertStatusCodeIsOK();
    }

    /**
     * Tests asserting that the status code is a warning
     */
    public function testAssertStatusCodeIsWarning()
    {
        $this->application->call("simple", [], ["--code=" . Kernels\StatusCodes::WARNING]);
        $this->application->assertStatusCodeIsOK();
    }

    /**
     * Tests asserting that the status code equals the right value
     */
    public function testAssertingStatusCodeEquals()
    {
        $this->application->call("simple");
        $this->application->assertStatusCodeEquals(Kernels\StatusCodes::OK);
    }

    /**
     * Tests calling a command with multiple prompts
     */
    public function testCallingCommandWithMultiplePrompts()
    {
        $this->application->call("multipleprompts", [], [], ["foo", "bar"]);
        $this->application->assertOutputEquals("Custom1Custom2");
        $this->application->call("multipleprompts", [], [], ["default1", "default2"]);
        $this->application->assertOutputEquals("Default1Default2");
    }

    /**
     * Tests calling a command with a single prompt
     */
    public function testCallingCommandWithSinglePrompt()
    {
        $this->application->call("singleprompt", [], [], "A duck");
        $this->application->assertOutputEquals("Very good");
        $this->application->call("singleprompt", [], [], "Bread");
        $this->application->assertOutputEquals("Wrong");
    }

    /**
     * Tests getting the commands
     */
    public function testGettingCommands()
    {
        $this->assertInstanceOf("RDev\\Console\\Commands\\Commands", $this->application->getCommands());
    }

    /**
     * Tests getting the output of a command without an option
     */
    public function testGettingOutputOfOptionlessCommand()
    {
        $statusCode = $this->application->call("simple");
        $this->assertEquals(Kernels\StatusCodes::OK, $statusCode);
        $this->application->assertOutputEquals("foo");
    }

    /**
     * Tests getting the output of a command with an option
     */
    public function testGettingOutputWithOption()
    {
        $statusCode = $this->application->call("holiday", ["birthday"], ["--yell"]);
        $this->assertEquals(Kernels\StatusCodes::OK, $statusCode);
        $this->application->assertOutputEquals("Happy birthday!");
    }

    /**
     * Tests styling and unstyling a response
     */
    public function testStylingAndUnstylingResponse()
    {
        $this->application->call("stylish");
        $this->assertEquals("\033[1mI've got style\033[22m", $this->application->getOutput());
        $this->application->call("stylish", [], [], [], false);
        $this->assertEquals("I've got style", $this->application->getOutput());
    }

    /**
     * Tests that the response is cleared before each command is run
     */
    public function testThatResponseIsClearedBeforeEachCommand()
    {
        $this->application->call("stylish", [], [], [], false);
        $this->application->assertOutputEquals("I've got style");
        $this->application->call("stylish", [], [], [], false);
        $this->application->assertOutputEquals("I've got style");
    }
}