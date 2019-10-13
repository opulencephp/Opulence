<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Console\Testing\PhpUnit;

use Aphiria\Console\App;
use Aphiria\Console\Commands\CommandRegistry;
use Aphiria\Console\Commands\ICommandBus;
use Aphiria\Console\Input\Compilers\IInputCompiler;
use Aphiria\Console\Input\Compilers\InputCompiler;
use Aphiria\Console\Output\Compilers\IOutputCompiler;
use Aphiria\Console\Output\Compilers\OutputCompiler;
use Aphiria\Console\Output\IOutput;
use Aphiria\Console\Output\Prompts\Prompt;
use Aphiria\Console\Output\StreamOutput;
use Aphiria\DependencyInjection\IContainer;
use Opulence\Framework\Console\Testing\PhpUnit\Assertions\OutputAssertions;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Defines the console integration test
 */
abstract class IntegrationTestCase extends TestCase
{
    /** @var IContainer The IoC container */
    protected IContainer $container;
    /** @var CommandRegistry The list of registered commands */
    protected CommandRegistry $commands;
    /** @var IInputCompiler|null The input compiler */
    protected ?IInputCompiler $inputCompiler = null;
    /** @var IOutputCompiler The output compiler */
    protected IOutputCompiler $outputCompiler;
    /** @var ICommandBus The console application */
    protected ICommandBus $app;
    /** @var OutputAssertions The output assertions */
    protected OutputAssertions $assertOutput;
    /** @var IOutput The output */
    protected IOutput $output;
    /** @var int The last status code */
    protected int $statusCode = -1;
    /** @var Prompt|MockObject The prompt to use in tests */
    protected Prompt $prompt;

    /**
     * Creates a command builder
     *
     * @param string $commandName The name of the command to build
     * @return CommandBuilder The command builder
     */
    public function command(string $commandName): CommandBuilder
    {
        return new CommandBuilder($this, $commandName);
    }

    /**
     * Executes a command to test
     *
     * @param string $commandName The name of the command to run
     * @param array $arguments The list of arguments
     * @param array $options The list of options
     * @param array|string $promptAnswers The answer or list of answers to use in any prompts
     * @param bool $isStyled Whether or not the output should be styled
     * @return self For method chaining
     */
    public function execute(
        string $commandName,
        array $arguments = [],
        array $options = [],
        $promptAnswers = [],
        bool $isStyled = true
    ): self {
        $promptAnswers = (array)$promptAnswers;

        if (count($promptAnswers) > 0) {
            $this->setPromptAnswers($promptAnswers);
        }

        // We instantiate the output every time so that it's fresh whenever a new command is called
        $this->output = new StreamOutput(\fopen('php://memory', 'wb'), fopen('php://stdin', 'rb'), $this->outputCompiler);
        $this->output->includeStyles($isStyled);
        $input = ['name' => $commandName, 'arguments' => $arguments, 'options' => $options];
        $this->statusCode = $this->app->handle($input, $this->output);
        $this->assertOutput->setOutput($this->output, $this->statusCode);

        return $this;
    }

    protected function setUp(): void
    {
        $this->commands = $this->container->resolve(CommandRegistry::class);

        if (!$this->container->tryResolve(IInputCompiler::class, $this->inputCompiler)) {
            $this->inputCompiler = new InputCompiler($this->commands);
        }

        $this->outputCompiler = new OutputCompiler();
        $this->app = new App($this->commands, $this->inputCompiler);
        $this->assertOutput = new OutputAssertions();

        // Bind a mock prompt that can output pre-determined answers
        $this->prompt = $this->createMock(Prompt::class);
        $this->container->bindInstance(Prompt::class, $this->prompt);
    }

    /**
     * Sets up the prompt to output pre-determined answers when asked
     *
     * @param array $answers The list of answers to return for each question
     */
    private function setPromptAnswers(array $answers): void
    {
        foreach ($answers as $index => $answer) {
            $this->prompt->expects($this->at($index))
                ->method('ask')
                ->willReturn($answer);
        }
    }
}
