<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the console kernel
 */
namespace RDev\Console\Kernels;
use Monolog;
use RDev\Console\Commands;
use RDev\Console\Commands\Compilers;
use RDev\Console\Requests;
use RDev\Console\Requests\Parsers;
use RDev\Console\Responses;

class Kernel
{
    /** @var Compilers\ICompiler The command compiler to use */
    private $commandCompiler = null;
    /** @var Commands\Commands The list of commands to choose from */
    private $commands = null;
    /** @var Monolog\Logger The logger to use */
    private $logger = null;

    /**
     * @param Compilers\ICompiler $commandCompiler The command compiler to use
     * @param Commands\Commands $commands The list of commands to choose from
     * @param Monolog\Logger $logger The logger to use
     */
    public function __construct(Compilers\ICompiler $commandCompiler, Commands\Commands $commands, Monolog\Logger $logger)
    {
        $this->commandCompiler = $commandCompiler;
        $this->commands = $commands;
        $this->logger = $logger;
    }

    /**
     * Handles a console command
     *
     * @param Parsers\IParser $requestParser The request parser
     * @param mixed $input The raw input to parse
     * @param Responses\IResponse $response The response to write to
     * @return int The status code
     */
    public function handle(Parsers\IParser $requestParser, $input, Responses\IResponse $response = null)
    {
        $request = $requestParser->parse($input);

        if($response === null)
        {
            $response = new Responses\Console();
        }

        if(!$this->commands->has($request->getCommandName()))
        {
            // TODO:  Handle missing command
        }

        $command = $this->commands->get($request->getCommandName());
        $compiledCommand = $this->commandCompiler->compile($command, $request);
        $compiledCommand->execute($response);

        // TODO:  Implement status code
    }
}