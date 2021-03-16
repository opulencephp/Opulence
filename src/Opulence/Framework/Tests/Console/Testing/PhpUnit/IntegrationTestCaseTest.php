<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Framework\Tests\Console\Testing\PhpUnit\Mocks;

use Opulence\Console\Commands\CommandCollection;
use Opulence\Console\Prompts\Prompt;
use Opulence\Console\Responses\Formatters\PaddingFormatter;
use Opulence\Console\Tests\Commands\Mocks\HappyHolidayCommand;
use Opulence\Console\Tests\Commands\Mocks\MultiplePromptsCommand;
use Opulence\Console\Tests\Commands\Mocks\SimpleCommand;
use Opulence\Console\Tests\Commands\Mocks\SinglePromptCommand;
use Opulence\Console\Tests\Commands\Mocks\StatusCodeCommand;
use Opulence\Console\Tests\Commands\Mocks\StyledCommand;
use Opulence\Framework\Console\Testing\PhpUnit\CommandBuilder;
use Opulence\Framework\Tests\Console\Testing\PhpUnit\Mocks\IntegrationTestCase as MockIntegrationTestCase;

/**
 * Tests the console integration test
 */
class IntegrationTestCaseTest extends \PHPUnit\Framework\TestCase
{
    /** @var MockIntegrationTestCase The console integration test to use in tests */
    private $testCase = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->testCase = new MockIntegrationTestCase();
        $this->testCase->setUp();
        $prompt = new Prompt(new PaddingFormatter());
        $this->testCase->getCommandCollection()->add(new SimpleCommand('simple', 'Simple command'));
        $this->testCase->getCommandCollection()->add(new StyledCommand());
        $this->testCase->getCommandCollection()->add(new HappyHolidayCommand());
        $this->testCase->getCommandCollection()->add(new StatusCodeCommand());
        $this->testCase->getCommandCollection()->add(new SinglePromptCommand($prompt));
        $this->testCase->getCommandCollection()->add(new MultiplePromptsCommand($prompt));
    }

    /**
     * Tests that call returns this
     */
    public function testCallReturnsThis()
    {
        $this->assertSame($this->testCase, $this->testCase->execute('simple'));
    }

    /**
     * Tests calling a command with multiple prompts
     */
    public function testCallingCommandWithMultiplePrompts()
    {
        $this->testCase->execute('multipleprompts', [], [], ['foo', 'bar'])
            ->getResponseAssertions()
            ->outputEquals('Custom1Custom2');
//        $this->testCase->execute('multipleprompts', [], [], ['default1', 'default2'])
//            ->getResponseAssertions()
//            ->outputEquals('Default1Default2');
    }

    /**
     * Tests calling a command with a single prompt
     */
    public function testCallingCommandWithSinglePrompt()
    {
        $this->testCase->execute('singleprompt', [], [], 'A duck')
            ->getResponseAssertions()
            ->outputEquals('Very good');
        $this->testCase->execute('singleprompt', [], [], 'Bread')
            ->getResponseAssertions()
            ->outputEquals('Wrong');
    }

    /**
     * Tests calling a non-existent command
     */
    public function testCallingNonExistentCommand()
    {
        // The About command should be run in this case
        $this->testCase->execute('doesnotexist')
            ->getResponseAssertions()
            ->isOK();
    }

    /**
     * Tests that a command builder is created
     */
    public function testCommandBuilderCreated()
    {
        $this->assertInstanceOf(CommandBuilder::class, $this->testCase->command('foo'));
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
        $this->testCase->execute('simple')
            ->getResponseAssertions()
            ->isOK()
            ->outputEquals('foo');
    }

    /**
     * Tests getting the output of a command with an option
     */
    public function testGettingOutputWithOption()
    {
        $this->testCase->execute('holiday', ['birthday'], ['--yell'])
            ->getResponseAssertions()
            ->isOK()
            ->outputEquals('Happy birthday!');
    }

    /**
     * Tests that the response assertions work
     */
    public function testResponseAssertionsWork()
    {
        $this->testCase->execute('simple')
            ->getResponseAssertions()
            ->isOK();
    }

    /**
     * Tests styling and unstyling a response
     */
    public function testStylingAndUnstylingResponse()
    {
        $this->testCase->execute('stylish')
            ->getResponseAssertions()
            ->outputEquals("\033[1mI've got style\033[22m");
        $this->testCase->execute('stylish', [], [], [], false)
            ->getResponseAssertions()
            ->outputEquals("I've got style");
    }

    /**
     * Tests that the response is cleared before each command is run
     */
    public function testThatResponseIsClearedBeforeEachCommand()
    {
        $this->testCase->execute('stylish', [], [], [], false)
            ->getResponseAssertions()
            ->outputEquals("I've got style");
        $this->testCase->execute('stylish', [], [], [], false)
            ->getResponseAssertions()
            ->outputEquals("I've got style");
    }
}
