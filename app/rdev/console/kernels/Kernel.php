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
    public function __construct(Compilers\ICompiler $commandCompiler, Commands\Commands &$commands, Monolog\Logger $logger)
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
        if($response === null)
        {
            $response = new Responses\Console();
        }

        try
        {
            $request = $requestParser->parse($input);

            if($this->isInvokingHelpCommand($request))
            {
                // We are going to execute the help command
                $compiledCommand = $this->getCompiledHelpCommand($request);
            }
            elseif($this->commands->has($request->getCommandName()))
            {
                // We are going to execute the command that was entered
                $command = $this->commands->get($request->getCommandName());
                $compiledCommand = $this->commandCompiler->compile($command, $request);
            }
            else
            {
                // We are defaulting to the About command
                $compiledCommand = new Commands\About($this->commands);
            }

            $statusCode = $compiledCommand->execute($response);

            if($statusCode === null)
            {
                return StatusCodes::OK;
            }

            return $statusCode;
        }
        catch(\Exception $ex)
        {
            $this->logger->addError($ex->getMessage());
            $response->writeln("Error: " . $ex->getMessage());

            return StatusCodes::ERROR;
        }
    }

    /**
     * Gets the compiled help command
     *
     * @param Requests\IRequest $request The parsed request
     * @return Commands\ICommand The compiled help command
     * @throws \InvalidArgumentException Thrown if the command that is requesting help does not exist
     */
    private function getCompiledHelpCommand(Requests\IRequest $request)
    {
        $helpCommand = new Commands\Help();

        if($request->getCommandName() == "help")
        {
            $compiledHelpCommand = $this->commandCompiler->compile($helpCommand, $request);
            $commandName = $compiledHelpCommand->getArgumentValue("command");
        }
        else
        {
            $commandName = $request->getCommandName();
        }

        if(!$this->commands->has($commandName))
        {
            throw new \InvalidArgumentException("No command with name \"$commandName\" exists");
        }

        $command = $this->commands->get($commandName);
        $helpCommand->setCommand($command);

        return $helpCommand;
    }

    /**
     * Gets whether or not the input is invoking the help command
     *
     * @param Requests\IRequest $request The parsed request
     * @return bool True if it is invoking the help command, otherwise false
     */
    private function isInvokingHelpCommand(Requests\IRequest $request)
    {
        return $request->getCommandName() == "help" || $request->optionIsSet("h") || $request->optionIsSet("help");
    }
}