<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the console kernel
 */
namespace RDev\Framework\Console;
use Exception;
use InvalidArgumentException;
use Monolog\Logger;
use RuntimeException;
use RDev\Console\Commands\AboutCommand;
use RDev\Console\Commands\CommandCollection;
use RDev\Console\Commands\Compilers\ICompiler as ICommandCompiler;
use RDev\Console\Commands\HelpCommand;
use RDev\Console\Commands\ICommand;
use RDev\Console\Commands\VersionCommand;
use RDev\Console\Requests\IRequest;
use RDev\Console\Requests\Parsers\IParser;
use RDev\Console\Responses\Compilers\Compiler;
use RDev\Console\Responses\Compilers\Lexers\Lexer;
use RDev\Console\Responses\Compilers\Parsers\Parser;
use RDev\Console\Responses\Console;
use RDev\Console\Responses\Formatters\CommandFormatter;
use RDev\Console\Responses\Formatters\PaddingFormatter;
use RDev\Console\Responses\IResponse;

class Kernel
{
    /** @var IParser The request parser to use */
    private $requestParser = null;
    /** @var ICommandCompiler The command compiler to use */
    private $commandCompiler = null;
    /** @var CommandCollection The list of commands to choose from */
    private $commandCollection = null;
    /** @var Logger The logger to use */
    private $logger = null;
    /** @var string The version number of the application */
    private $applicationVersion = "Unknown";

    /**
     * @param IParser $requestParser The request parser to use
     * @param ICommandCompiler $commandCompiler The command compiler to use
     * @param CommandCollection $commandCollection The list of commands to choose from
     * @param Logger $logger The logger to use
     * @param string $applicationVersion The version number of the application
     */
    public function __construct(
        IParser $requestParser,
        ICommandCompiler $commandCompiler,
        CommandCollection &$commandCollection,
        Logger $logger,
        $applicationVersion = "Unknown"
    )
    {
        $this->requestParser = $requestParser;
        $this->commandCompiler = $commandCompiler;
        $this->commandCollection = $commandCollection;
        $this->logger = $logger;
        $this->applicationVersion = $applicationVersion;
    }

    /**
     * Handles a console command
     *
     * @param mixed $input The raw input to parse
     * @param IResponse $response The response to write to
     * @return int The status code
     */
    public function handle($input, IResponse $response = null)
    {
        if($response === null)
        {
            $response = new Console(new Compiler(new Lexer(), new Parser()));
        }

        try
        {
            $request = $this->requestParser->parse($input);

            if($this->isInvokingHelpCommand($request))
            {
                // We are going to execute the help command
                $compiledCommand = $this->getCompiledHelpCommand($request);
            }
            elseif($this->isInvokingVersionCommand($request))
            {
                // We are going to execute the version command
                $compiledCommand = new VersionCommand($this->applicationVersion);
            }
            elseif($this->commandCollection->has($request->getCommandName()))
            {
                // We are going to execute the command that was entered
                $command = $this->commandCollection->get($request->getCommandName());
                $compiledCommand = $this->commandCompiler->compile($command, $request);
            }
            else
            {
                // We are defaulting to the about command
                $compiledCommand = new AboutCommand($this->commandCollection, new PaddingFormatter(), $this->applicationVersion);
            }

            $statusCode = $compiledCommand->execute($response);

            if($statusCode === null)
            {
                return StatusCodes::OK;
            }

            return $statusCode;
        }
        catch(InvalidArgumentException $ex)
        {
            $response->writeln("<error>{$ex->getMessage()}</error>");

            return StatusCodes::ERROR;
        }
        catch(RuntimeException $ex)
        {
            $response->writeln("<fatal>{$ex->getMessage()}</fatal>");

            return StatusCodes::FATAL;
        }
        catch(Exception $ex)
        {
            $response->writeln("<fatal>{$ex->getMessage()}</fatal>");
            $this->logger->addError($ex->getMessage());

            return StatusCodes::FATAL;
        }
    }

    /**
     * Gets the compiled help command
     *
     * @param IRequest $request The parsed request
     * @return ICommand The compiled help command
     * @throws InvalidArgumentException Thrown if the command that is requesting help does not exist
     */
    private function getCompiledHelpCommand(IRequest $request)
    {
        $helpCommand = new HelpCommand(new CommandFormatter(), new PaddingFormatter());
        $commandName = null;

        if($request->getCommandName() == "help")
        {
            $compiledHelpCommand = $this->commandCompiler->compile($helpCommand, $request);

            if($compiledHelpCommand->argumentValueIsSet("command"))
            {
                $commandName = $compiledHelpCommand->getArgumentValue("command");
            }
        }
        else
        {
            $commandName = $request->getCommandName();
        }

        // Set the command only if it was passed as an argument to the help command
        if($commandName !== null && $commandName !== "")
        {
            if(!$this->commandCollection->has($commandName))
            {
                throw new InvalidArgumentException("No command with name \"$commandName\" exists");
            }

            $command = $this->commandCollection->get($commandName);
            $helpCommand->setCommand($command);
        }

        return $helpCommand;
    }

    /**
     * Gets whether or not the input is invoking the help command
     *
     * @param IRequest $request The parsed request
     * @return bool True if it is invoking the help command, otherwise false
     */
    private function isInvokingHelpCommand(IRequest $request)
    {
        return $request->getCommandName() == "help" || $request->optionIsSet("h") || $request->optionIsSet("help");
    }

    /**
     * Gets whether or not the input is invoking the version command
     *
     * @param IRequest $request The parsed request
     * @return bool True if it is invoking the version command, otherwise false
     */
    private function isInvokingVersionCommand(IRequest $request)
    {
        return $request->optionIsSet("v") || $request->optionIsSet("version");
    }
}