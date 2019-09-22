<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

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
    private MockIntegrationTestCase $testCase;

    protected function setUp(): void
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

    public function testCallReturnsThis(): void
    {
        $this->assertSame($this->testCase, $this->testCase->execute('simple'));
    }

    public function testCallingCommandWithMultiplePrompts(): void
    {
        $this->testCase->execute('multipleprompts', [], [], ['foo', 'bar'])
            ->getResponseAssertions()
            ->outputEquals('Custom1Custom2');
        $this->testCase->execute('multipleprompts', [], [], ['default1', 'default2'])
            ->getResponseAssertions()
            ->outputEquals('Default1Default2');
    }

    public function testCallingCommandWithSinglePrompt(): void
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
    public function testCallingNonExistentCommand(): void
    {
        // The About command should be run in this case
        $this->testCase->execute('doesnotexist')
            ->getResponseAssertions()
            ->isOK();
    }

    public function testCommandBuilderCreated(): void
    {
        $this->assertInstanceOf(CommandBuilder::class, $this->testCase->command('foo'));
    }

    public function testGettingCommands(): void
    {
        $this->assertInstanceOf(CommandCollection::class, $this->testCase->getCommandCollection());
    }

    public function testGettingOutputOfOptionlessCommand(): void
    {
        $this->testCase->execute('simple')
            ->getResponseAssertions()
            ->isOK()
            ->outputEquals('foo');
    }

    public function testGettingOutputWithOption(): void
    {
        $this->testCase->execute('holiday', ['birthday'], ['--yell'])
            ->getResponseAssertions()
            ->isOK()
            ->outputEquals('Happy birthday!');
    }

    public function testResponseAssertionsWork(): void
    {
        $this->testCase->execute('simple')
            ->getResponseAssertions()
            ->isOK();
    }

    public function testStylingAndUnstylingResponse(): void
    {
        $this->testCase->execute('stylish')
            ->getResponseAssertions()
            ->outputEquals("\033[1mI've got style\033[22m");
        $this->testCase->execute('stylish', [], [], [], false)
            ->getResponseAssertions()
            ->outputEquals("I've got style");
    }

    public function testThatResponseIsClearedBeforeEachCommand(): void
    {
        $this->testCase->execute('stylish', [], [], [], false)
            ->getResponseAssertions()
            ->outputEquals("I've got style");
        $this->testCase->execute('stylish', [], [], [], false)
            ->getResponseAssertions()
            ->outputEquals("I've got style");
    }
}
