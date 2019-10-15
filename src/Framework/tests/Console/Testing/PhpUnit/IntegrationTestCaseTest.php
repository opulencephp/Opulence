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

use Aphiria\Console\Commands\ClosureCommandHandler;
use Aphiria\Console\Commands\Command;
use Aphiria\Console\Commands\ICommandHandler;
use Aphiria\Console\Input\Input;
use Aphiria\Console\Input\Option;
use Aphiria\Console\Input\OptionTypes;
use Aphiria\Console\Output\IOutput;
use Aphiria\Console\Output\Prompts\Prompt;
use Aphiria\Console\Output\Prompts\Question;
use Opulence\Framework\Tests\Console\Testing\PhpUnit\Mocks\IntegrationTestCase as MockIntegrationTestCase;
use PHPUnit\Framework\TestCase;

/**
 * Tests the console integration test
 */
class IntegrationTestCaseTest extends TestCase
{
    private MockIntegrationTestCase $testCase;

    protected function setUp(): void
    {
        $this->testCase = new MockIntegrationTestCase();
        $this->testCase->setUp();
    }

    public function testCallReturnsThis(): void
    {
        $this->assertSame($this->testCase, $this->testCase->execute('simple'));
    }

    public function testCallingCommandWithMultiplePrompts(): void
    {
        $this->testCase->getCommands()->registerCommand(
            new Command('multipleprompts', [], [], ''),
            fn () => new class ($this->testCase->getContainer()->resolve(Prompt::class)) implements ICommandHandler {
                private Prompt $prompt;

                public function __construct(Prompt $prompt)
                {
                    $this->prompt = $prompt;
                }

                public function handle(Input $input, IOutput $output)
                {
                    $question1 = new Question('Q1', 'default1');
                    $question2 = new Question('Q2', 'default2');
                    $answer1 = $this->prompt->ask($question1, $output);
                    $answer2 = $this->prompt->ask($question2, $output);

                    if ($answer1 === 'default1') {
                        $output->write('Default1');
                    } else {
                        $output->write('Custom1');
                    }

                    if ($answer2 === 'default2') {
                        $output->write('Default2');
                    } else {
                        $output->write('Custom2');
                    }
                }
            }
        );
        $this->testCase->execute('multipleprompts', [], [], ['foo', 'bar'])
            ->getOutputAssertions()
            ->outputEquals('Custom1Custom2');
        $this->testCase->execute('multipleprompts', [], [], ['default1', 'default2'])
            ->getOutputAssertions()
            ->outputEquals('Default1Default2');
    }

    public function testCallingCommandWithSinglePrompt(): void
    {
        $this->testCase->getCommands()->registerCommand(
            new Command('singleprompt', [], [], ''),
            fn () => new class ($this->testCase->getContainer()->resolve(Prompt::class)) implements ICommandHandler {
                private Prompt $prompt;

                public function __construct(Prompt $prompt)
                {
                    $this->prompt = $prompt;
                }

                public function handle(Input $input, IOutput $output)
                {
                    $question = new Question('What else floats', 'Very small rocks');
                    $answer = $this->prompt->ask($question, $output);

                    if ($answer === 'A duck') {
                        $output->write('Very good');
                    } else {
                        $output->write('Wrong');
                    }
                }
            }
        );
        $this->testCase->execute('singleprompt', [], [], 'A duck')
            ->getOutputAssertions()
            ->outputEquals('Very good');
        $this->testCase->execute('singleprompt', [], [], 'Bread')
            ->getOutputAssertions()
            ->outputEquals('Wrong');
    }

    public function testGettingOutputOfOptionlessCommand(): void
    {
        $this->testCase->getCommands()->registerCommand(
            new Command('simple', [], [], ''),
            fn () => new ClosureCommandHandler(fn (Input $input, IOutput $output) => $output->write('foo'))
        );
        $this->testCase->execute('simple')
            ->getOutputAssertions()
            ->isOk()
            ->outputEquals('foo');
    }

    public function testGettingOutputWithOption(): void
    {
        $this->testCase->getCommands()->registerCommand(
            new Command(
                'hi',
                [],
                [
                    new Option(
                        'yell',
                        'y',
                        OptionTypes::OPTIONAL_VALUE,
                        'Whether or not we yell',
                        'yes'
                    )
                ],
                ''
            ),
            fn () => new ClosureCommandHandler(function (Input $input, IOutput $output) {
                $message = 'Hi';

                if ($input->options['yell'] === 'yes') {
                    $message .= '!';
                }

                $output->write($message);
            })
        );
        $this->testCase->execute('hi', [], ['--yell'])
            ->getOutputAssertions()
            ->isOk()
            ->outputEquals('Hi!');
    }

    public function testStylingAndUnstylingOutput(): void
    {
        $this->testCase->getCommands()->registerCommand(
            new Command('foo', [], [], ''),
            fn () => new ClosureCommandHandler(fn (Input $input, IOutput $output) => $output->write('<b>bar</b>'))
        );
        $this->testCase->execute('foo')
            ->getOutputAssertions()
            ->outputEquals("\033[1mbar\033[22m");
        $this->testCase->execute('foo', [], [], [], false)
            ->getOutputAssertions()
            ->outputEquals('bar');
    }

    public function testThatOutputIsClearedBeforeEachCommand(): void
    {
        $this->testCase->getCommands()->registerCommand(
            new Command('foo', [], [], ''),
            fn () => new ClosureCommandHandler(fn (Input $input, IOutput $output) => $output->write('<b>bar</b>'))
        );
        $this->testCase->execute('foo', [], [], [], false)
            ->getOutputAssertions()
            ->outputEquals('bar');
        $this->testCase->execute('foo', [], [], [], false)
            ->getOutputAssertions()
            ->outputEquals('bar');
    }
}
