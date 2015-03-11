<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the console application test case
 */
namespace RDev\Framework\Tests;
use RDev\Console\Commands;
use RDev\Console\Commands\Compilers as CommandCompilers;
use RDev\Console\Kernels;
use RDev\Console\Requests\Parsers as RequestParsers;
use RDev\Console\Responses;
use RDev\Console\Responses\Compilers as ResponseCompilers;
use RDev\Console\Responses\Compilers\Lexers as ResponseLexers;
use RDev\Console\Responses\Compilers\Parsers as ResponseParsers;
use RDev\Console\Responses\Formatters;

abstract class ConsoleApplicationTestCase extends ApplicationTestCase
{
    /** @var Commands\Commands The list of registered commands */
    protected $commands = null;
    /** @var CommandCompilers\ICompiler The command compiler */
    protected $commandCompiler = null;
    /** @var ResponseCompilers\ICompiler The response compiler */
    protected $responseCompiler = null;
    /** @var Kernels\Kernel The console kernel */
    protected $kernel = null;
    /** @var RequestParsers\IParser The request parser */
    protected $requestParser = null;
    /** @var Responses\Stream The response stream */
    protected $response = null;
    /** @var int The status code */
    protected $statusCode = 0;
    /** @var \PHPUnit_Framework_MockObject_MockObject The prompt to use in tests */
    protected $prompt = null;

    /**
     * Asserts that the output is an expected value
     *
     * @param string $expected The expected output
     */
    public function assertOutputEquals($expected)
    {
        $this->checkResponseIsSet();
        $this->assertEquals($expected, $this->getOutput());
    }

    /**
     * Asserts that the status code equals an expected value
     *
     * @param int $expected The expected status code
     */
    public function assertStatusCodeEquals($expected)
    {
        $this->checkResponseIsSet();
        $this->assertEquals($expected, $this->statusCode);
    }

    /**
     * Asserts that the status code is an error
     */
    public function assertStatusCodeIsError()
    {
        $this->assertStatusCodeEquals(Kernels\StatusCodes::ERROR);
    }

    /**
     * Asserts that the status code is fatal
     */
    public function assertStatusCodeIsFatal()
    {
        $this->assertStatusCodeEquals(Kernels\StatusCodes::FATAL);
    }

    /**
     * Asserts that the status code is OK
     */
    public function assertStatusCodeIsOK()
    {
        $this->assertStatusCodeEquals(Kernels\StatusCodes::OK);
    }

    /**
     * Asserts that the status code is a warning
     */
    public function assertStatusCodeIsWarning()
    {
        $this->assertStatusCodeEquals(Kernels\StatusCodes::WARNING);
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
     * @throws \InvalidArgumentException Thrown if no command exists with the input name
     */
    public function call(
        $commandName,
        array $arguments = [],
        array $options = [],
        $promptAnswers = [],
        $isStyled = true
    )
    {
        $promptAnswers = (array)$promptAnswers;

        if(count($promptAnswers) > 0)
        {
            $this->setPromptAnswers($commandName, $promptAnswers);
        }

        // We instantiate the response every time so that it's fresh every time a new command is called
        $this->response = new Responses\Stream(fopen("php://memory", "w"), $this->responseCompiler);
        $input = ["name" => $commandName, "arguments" => $arguments, "options" => $options];
        $this->response->setStyled($isStyled);
        $this->statusCode = $this->kernel->handle($input, $this->response);

        return $this->statusCode;
    }

    /**
     * @return CommandCompilers\ICompiler
     */
    public function getCommandCompiler()
    {
        return $this->commandCompiler;
    }

    /**
     * @return Commands\Commands
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * @return Kernels\Kernel
     */
    public function getKernel()
    {
        return $this->kernel;
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
     * @return RequestParsers\IParser
     */
    public function getRequestParser()
    {
        return $this->requestParser;
    }

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->setApplication();
        $this->application->start();
        $this->requestParser = new RequestParsers\ArrayList();
        $container = $this->application->getIoCContainer();
        $this->commands = $container->makeShared("RDev\\Console\\Commands\\Commands");
        $this->commandCompiler = $container->makeShared("RDev\\Console\\Commands\\Compilers\\ICompiler");
        $this->responseCompiler = new ResponseCompilers\Compiler(new ResponseLexers\Lexer(), new ResponseParsers\Parser());
        $this->kernel = new Kernels\Kernel(
            $this->requestParser,
            $this->commandCompiler,
            $this->commands,
            $this->application->getLogger(),
            $this->application->getVersion()
        );

        // Bind a mock prompt that can output pre-determined answers
        $promptClassName = "RDev\\Console\\Prompts\\Prompt";
        $this->prompt = $this->getMock($promptClassName, ["ask"], [new Formatters\Padding()]);
        $this->application->getIoCContainer()->bind($promptClassName, $this->prompt);
    }

    /**
     * Checks if the response was set
     * Useful for making sure the response was set before making any assertions on it
     */
    private function checkResponseIsSet()
    {
        if($this->response === null)
        {
            $this->fail("Must call route() before assertions");
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
        $commandClassName = get_class($this->commands->get($commandName));

        foreach($answers as $index => $answer)
        {
            $this->prompt->expects($this->at($index))
                ->method("ask")
                ->willReturn($answer);
        }

        // Remake the command to have this latest binding
        $this->commands->add($this->application->getIoCContainer()->makeShared($commandClassName), true);
    }
}