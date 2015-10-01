<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the console application tester
 */
namespace Opulence\Framework\Tests\Console;

use Opulence\Applications\Environments\Environment;
use Opulence\Console\Commands\CommandCollection;
use Opulence\Console\Prompts\Prompt;
use Opulence\Console\Responses\Formatters\PaddingFormatter;
use Opulence\Framework\Console\StatusCodes;
use Opulence\Tests\Console\Commands\Mocks\HappyHolidayCommand;
use Opulence\Tests\Console\Commands\Mocks\MultiplePromptsCommand;
use Opulence\Tests\Console\Commands\Mocks\SimpleCommand;
use Opulence\Tests\Console\Commands\Mocks\SinglePromptCommand;
use Opulence\Tests\Console\Commands\Mocks\StatusCodeCommand;
use Opulence\Tests\Console\Commands\Mocks\StyledCommand;
use Opulence\Tests\Framework\Tests\Console\Mocks\ApplicationTestCase;

class ApplicationTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /** @var ApplicationTestCase The console application to use in tests */
    private $testCase = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->testCase = new ApplicationTestCase();
        $this->testCase->setUp();
        $prompt = new Prompt(new PaddingFormatter());
        $this->testCase->getCommandCollection()->add(new SimpleCommand("simple", "Simple command"));
        $this->testCase->getCommandCollection()->add(new StyledCommand());
        $this->testCase->getCommandCollection()->add(new HappyHolidayCommand());
        $this->testCase->getCommandCollection()->add(new StatusCodeCommand());
        $this->testCase->getCommandCollection()->add(new SinglePromptCommand($prompt));
        $this->testCase->getCommandCollection()->add(new MultiplePromptsCommand($prompt));
    }

    /**
     * Tests asserting that the status code is an error
     */
    public function testAssertStatusCodeIsError()
    {
        $this->testCase->call("simple", [], ["--code=" . StatusCodes::ERROR]);
        $this->testCase->assertStatusCodeIsOK();
    }

    /**
     * Tests asserting that the status code is fatal
     */
    public function testAssertStatusCodeIsFatal()
    {
        $this->testCase->call("simple", [], ["--code=" . StatusCodes::FATAL]);
        $this->testCase->assertStatusCodeIsOK();
    }

    /**
     * Tests asserting that the status code is OK
     */
    public function testAssertStatusCodeIsOK()
    {
        $this->testCase->call("simple", [], ["--code=" . StatusCodes::OK]);
        $this->testCase->assertStatusCodeIsOK();
    }

    /**
     * Tests asserting that the status code is a warning
     */
    public function testAssertStatusCodeIsWarning()
    {
        $this->testCase->call("simple", [], ["--code=" . StatusCodes::WARNING]);
        $this->testCase->assertStatusCodeIsOK();
    }

    /**
     * Tests asserting that the status code equals the right value
     */
    public function testAssertingStatusCodeEquals()
    {
        $this->testCase->call("simple");
        $this->testCase->assertStatusCodeEquals(StatusCodes::OK);
    }

    /**
     * Tests calling a command with multiple prompts
     */
    public function testCallingCommandWithMultiplePrompts()
    {
        $this->testCase->call("multipleprompts", [], [], ["foo", "bar"]);
        $this->testCase->assertOutputEquals("Custom1Custom2");
        $this->testCase->call("multipleprompts", [], [], ["default1", "default2"]);
        $this->testCase->assertOutputEquals("Default1Default2");
    }

    /**
     * Tests calling a command with a single prompt
     */
    public function testCallingCommandWithSinglePrompt()
    {
        $this->testCase->call("singleprompt", [], [], "A duck");
        $this->testCase->assertOutputEquals("Very good");
        $this->testCase->call("singleprompt", [], [], "Bread");
        $this->testCase->assertOutputEquals("Wrong");
    }

    /**
     * Tests calling a non-existent command
     */
    public function testCallingNonExistentCommand()
    {
        $this->testCase->call("doesnotexist");
        // The About command should be run in this case
        $this->testCase->assertStatusCodeIsOK();
    }

    /**
     * Tests getting the commands
     */
    public function testGettingCommands()
    {
        $this->assertInstanceOf(CommandCollection::class, $this->testCase->getCommandCollection());
    }

    /**
     * Tests getting the output of a command without an option
     */
    public function testGettingOutputOfOptionlessCommand()
    {
        $statusCode = $this->testCase->call("simple");
        $this->assertEquals(StatusCodes::OK, $statusCode);
        $this->testCase->assertOutputEquals("foo");
    }

    /**
     * Tests getting the output of a command with an option
     */
    public function testGettingOutputWithOption()
    {
        $statusCode = $this->testCase->call("holiday", ["birthday"], ["--yell"]);
        $this->assertEquals(StatusCodes::OK, $statusCode);
        $this->testCase->assertOutputEquals("Happy birthday!");
    }

    /**
     * Tests styling and unstyling a response
     */
    public function testStylingAndUnstylingResponse()
    {
        $this->testCase->call("stylish");
        $this->assertEquals("\033[1mI've got style\033[22m", $this->testCase->getOutput());
        $this->testCase->call("stylish", [], [], [], false);
        $this->assertEquals("I've got style", $this->testCase->getOutput());
    }

    /**
     * Tests that the testing environment is set
     */
    public function testTestingEnvironmentIsSet()
    {
        $this->assertEquals(Environment::TESTING, $this->testCase->getApplication()->getEnvironment()->getName());
    }

    /**
     * Tests that the response is cleared before each command is run
     */
    public function testThatResponseIsClearedBeforeEachCommand()
    {
        $this->testCase->call("stylish", [], [], [], false);
        $this->testCase->assertOutputEquals("I've got style");
        $this->testCase->call("stylish", [], [], [], false);
        $this->testCase->assertOutputEquals("I've got style");
    }
}