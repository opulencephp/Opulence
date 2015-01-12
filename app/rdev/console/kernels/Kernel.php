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
use RDev\Console\Responses\Formatters;

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
                $compiledCommand = new Commands\About($this->commands, new Formatters\Padding());
            }

            $statusCode = $compiledCommand->execute($response);

            if($statusCode === null)
            {
                return StatusCodes::OK;
            }

            return $statusCode;
        }
        catch(\InvalidArgumentException $ex)
        {
            $response->writeln("Error: " . $ex->getMessage());

            return StatusCodes::ERROR;
        }
        catch(\RuntimeException $ex)
        {
            $response->writeln("Fatal: " . $ex->getMessage());

            return StatusCodes::FATAL;
        }
        catch(\Exception $ex)
        {
            $response->writeln("Fatal: " . $ex->getMessage());
            $this->logger->addError($ex->getMessage());

            return StatusCodes::FATAL;
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
        $helpCommand = new Commands\Help(new Formatters\Command(), new Formatters\Padding());
        $commandName = null;

        if($request->getCommandName() == "help")
        {
            $compiledHelpCommand = $this->commandCompiler->compile($helpCommand, $request);

            if($compiledHelpCommand->argumentHasValue("command"))
            {
                $commandName = $compiledHelpCommand->getArgumentValue("command");
            }
        }
        else
        {
            $commandName = $request->getCommandName();
        }

        // Set the command only if it was passed as an argument to the help command
        if($commandName !== null)
        {
            if(!$this->commands->has($commandName))
            {
                throw new \InvalidArgumentException("No command with name \"$commandName\" exists");
            }

            $command = $this->commands->get($commandName);
            $helpCommand->setCommand($command);
        }

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