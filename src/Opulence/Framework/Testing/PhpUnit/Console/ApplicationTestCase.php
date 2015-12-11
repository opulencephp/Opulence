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
use Opulence\Console\Responses\Response;
use Opulence\Console\Responses\StreamResponse;
use Opulence\Environments\Environment;
use Opulence\Framework\Testing\PhpUnit\ApplicationTestCase as BaseApplicationTestCase;
use Opulence\Framework\Testing\PhpUnit\Console\Assertions\ResponseAssertions;
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
    /** @var ResponseAssertions The response assertions */
    protected $assertResponse = null;
    /** @var Response The last response */
    protected $response = null;
    /** @var int The last status code */
    protected $statusCode = -1;
    /** @var PHPUnit_Framework_MockObject_MockObject The prompt to use in tests */
    protected $prompt = null;

    /**
     * Creates a command builder
     *
     * @param string $commandName The name of the command to build
     * @return CommandBuilder The command builder
     */
    public function command($commandName)
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
     * @return $this For method chaining
     */
    public function execute(
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
        $this->assertResponse->setResponse($this->response, $this->statusCode);

        return $this;
    }

    /**
     * @return CommandCollection
     */
    public function getCommandCollection()
    {
        return $this->commandCollection;
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
        $this->assertResponse = new ResponseAssertions();

        // Bind a mock prompt that can output pre-determined answers
        $this->prompt = $this->getMock(Prompt::class, ["ask"], [new PaddingFormatter()]);
        $this->container->bind(Prompt::class, $this->prompt);
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