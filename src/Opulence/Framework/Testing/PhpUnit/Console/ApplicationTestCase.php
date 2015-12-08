<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Testing\PhpUnit\Console;

use Opulence\Console\Commands\CommandCollection;
use Opulence\Console\Commands\Compilers\ICompiler;
use Opulence\Console\Kernel;
use Opulence\Console\Prompts\Prompt;
use Opulence\Console\Requests\Parsers\ArrayListParser;
use Opulence\Console\Requests\Parsers\IParser as IRequestParser;
use Opulence\Console\Responses\Compilers\Compiler as ResponseCompiler;
use Opulence\Console\Responses\Compilers\ICompiler as IResponseCompiler;
use Opulence\Console\Responses\Compilers\Lexers\Lexer as ResponseLexer;
use Opulence\Console\Responses\Compilers\Parsers\Parser as ResponseParser;
use Opulence\Console\Responses\Formatters\PaddingFormatter;
use Opulence\Console\Responses\StreamResponse;
use Opulence\Console\StatusCodes;
use Opulence\Environments\Environment;
use Opulence\Framework\Testing\PhpUnit\ApplicationTestCase as BaseApplicationTestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Defines the console application test case
 */
abstract class ApplicationTestCase extends BaseApplicationTestCase
{
    /** @var CommandCollection The list of registered commands */
    protected $commandCollection = null;
    /** @var ICompiler The command compiler */
    protected $commandCompiler = null;
    /** @var IResponseCompiler The response compiler */
    protected $responseCompiler = null;
    /** @var Kernel The console kernel */
    protected $kernel = null;
    /** @var IRequestParser The request parser */
    protected $requestParser = null;
    /** @var StreamResponse The response stream */
    protected $response = null;
    /** @var int The status code */
    protected $statusCode = 0;
    /** @var PHPUnit_Framework_MockObject_MockObject The prompt to use in tests */
    protected $prompt = null;

    /**
     * Asserts that the output is an expected value
     *
     * @param string $expected The expected output
     * @return $this For method chaining
     */
    public function assertOutputEquals($expected)
    {
        $this->checkResponseIsSet();
        $this->assertEquals($expected, $this->getOutput());

        return $this;
    }

    /**
     * Asserts that the status code equals an expected value
     *
     * @param int $expected The expected status code
     * @return $this For method chaining
     */
    public function assertStatusCodeEquals($expected)
    {
        $this->checkResponseIsSet();
        $this->assertEquals($expected, $this->statusCode);

        return $this;
    }

    /**
     * Asserts that the status code is an error
     *
     * @return $this For method chaining
     */
    public function assertStatusCodeIsError()
    {
        $this->assertStatusCodeEquals(StatusCodes::ERROR);

        return $this;
    }

    /**
     * Asserts that the status code is fatal
     *
     * @return $this For method chaining
     */
    public function assertStatusCodeIsFatal()
    {
        $this->assertStatusCodeEquals(StatusCodes::FATAL);

        return $this;
    }

    /**
     * Asserts that the status code is OK
     *
     * @return $this For method chaining
     */
    public function assertStatusCodeIsOK()
    {
        $this->assertStatusCodeEquals(StatusCodes::OK);

        return $this;
    }

    /**
     * Asserts that the status code is a warning
     *
     * @return $this For method chaining
     */
    public function assertStatusCodeIsWarning()
    {
        $this->assertStatusCodeEquals(StatusCodes::WARNING);

        return $this;
    }

    /**
     * Calls a command to test
     *
     * @param string $commandName The name of the command to run
     * @param array $arguments The list of arguments
     * @param array $options The list of options
     * @param array|string $promptAnswers The answer or list of answers to use in any prompts
     * @param bool $isStyled Whether or not the output should be styled
     * @return int The status code of the command
     */
    public function call(
        $commandName,
        array $arguments = [],
        array $options = [],
        $promptAnswers = [],
        $isStyled = true
    ) {
        $promptAnswers = (array)$promptAnswers;

        if (count($promptAnswers) > 0) {
            $this->setPromptAnswers($commandName, $promptAnswers);
        }

        // We instantiate the response every time so that it's fresh whenever a new command is called
        $this->response = new StreamResponse(fopen("php://memory", "w"), $this->responseCompiler);
        $this->response->setStyled($isStyled);
        $input = ["name" => $commandName, "arguments" => $arguments, "options" => $options];
        $this->statusCode = $this->kernel->handle($input, $this->response);

        return $this->statusCode;
    }

    /**
     * @return CommandCollection
     */
    public function getCommandCollection()
    {
        return $this->commandCollection;
    }

    /**
     * Gets the output of the previous command
     *
     * @return string The output
     */
    public function getOutput()
    {
        $this->checkResponseIsSet();
        rewind($this->response->getStream());

        return stream_get_contents($this->response->getStream());
    }

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->environment->setName(Environment::TESTING);
        $this->application->start();
        $this->requestParser = new ArrayListParser();
        $this->commandCollection = $this->container->makeShared(CommandCollection::class);
        $this->commandCompiler = $this->container->makeShared(ICompiler::class);
        $this->responseCompiler = new ResponseCompiler(new ResponseLexer(), new ResponseParser());
        $this->kernel = new Kernel(
            $this->requestParser,
            $this->commandCompiler,
            $this->commandCollection,
            $this->application->getVersion()
        );

        // Bind a mock prompt that can output pre-determined answers
        $this->prompt = $this->getMock(Prompt::class, ["ask"], [new PaddingFormatter()]);
        $this->container->bind(Prompt::class, $this->prompt);
    }

    /**
     * Checks if the response was set
     * Useful for making sure the response was set before making any assertions on it
     */
    private function checkResponseIsSet()
    {
        if ($this->response === null) {
            $this->fail("Must call call() before assertions");
        }
    }

    /**
     * Sets up the prompt to output pre-determined answers when asked
     *
     * @param string $commandName The name of the command
     * @param array $answers The list of answers to return for each question
     */
    private function setPromptAnswers($commandName, array $answers)
    {
        $commandClassName = get_class($this->commandCollection->get($commandName));

        foreach ($answers as $index => $answer) {
            $this->prompt->expects($this->at($index))
                ->method("ask")
                ->willReturn($answer);
        }

        // Remake the command to have this latest binding
        $this->commandCollection->add($this->container->makeShared($commandClassName), true);
    }
}