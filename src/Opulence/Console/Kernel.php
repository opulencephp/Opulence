<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Console;

use Exception;
use InvalidArgumentException;
use Opulence\Console\Commands\AboutCommand;
use Opulence\Console\Commands\CommandCollection;
use Opulence\Console\Commands\Compilers\ICompiler as ICommandCompiler;
use Opulence\Console\Commands\HelpCommand;
use Opulence\Console\Commands\ICommand;
use Opulence\Console\Commands\VersionCommand;
use Opulence\Console\Requests\IRequest;
use Opulence\Console\Requests\Parsers\IParser;
use Opulence\Console\Responses\Compilers\Compiler;
use Opulence\Console\Responses\Compilers\Lexers\Lexer;
use Opulence\Console\Responses\Compilers\Parsers\Parser;
use Opulence\Console\Responses\ConsoleResponse;
use Opulence\Console\Responses\Formatters\CommandFormatter;
use Opulence\Console\Responses\Formatters\PaddingFormatter;
use Opulence\Console\Responses\IResponse;
use Throwable;

/**
 * Defines the console kernel
 */
class Kernel
{
    /** @var IParser The request parser to use */
    private $requestParser = null;
    /** @var ICommandCompiler The command compiler to use */
    private $commandCompiler = null;
    /** @var CommandCollection The list of commands to choose from */
    private $commandCollection = null;
    /** @var string The version number of the application */
    private $applicationVersion = 'Unknown';

    /**
     * @param IParser $requestParser The request parser to use
     * @param ICommandCompiler $commandCompiler The command compiler to use
     * @param CommandCollection $commandCollection The list of commands to choose from
     * @param string $applicationVersion The version number of the application
     * @deprecated 1.1.0 The $applicationVersion parameter will soon not be accepted
     */
    public function __construct(
        IParser $requestParser,
        ICommandCompiler $commandCompiler,
        CommandCollection $commandCollection,
        string $applicationVersion = 'Unknown'
    ) {
        $this->requestParser = $requestParser;
        $this->commandCompiler = $commandCompiler;
        $this->commandCollection = $commandCollection;
        $this->applicationVersion = $applicationVersion;
    }

    /**
     * Handles a console command
     *
     * @param mixed $input The raw input to parse
     * @param ?IResponse $response The response to write to
     * @return int The status code
     */
    public function handle($input, IResponse $response = null) : int
    {
        if ($response === null) {
            $response = new ConsoleResponse(new Compiler(new Lexer(), new Parser()));
        }

        try {
            $request = $this->requestParser->parse($input);

            if ($this->isInvokingHelpCommand($request)) {
                // We are going to execute the help command
                $compiledCommand = $this->getCompiledHelpCommand($request);
            } elseif ($this->isInvokingVersionCommand($request)) {
                // We are going to execute the version command
                $compiledCommand = new VersionCommand($this->applicationVersion);
            } elseif ($this->commandCollection->has($request->getCommandName())) {
                // We are going to execute the command that was entered
                $command = $this->commandCollection->get($request->getCommandName());
                $compiledCommand = $this->commandCompiler->compile($command, $request);
            } else {
                // We are defaulting to the about command
                $compiledCommand = new AboutCommand($this->commandCollection, new PaddingFormatter(),
                    $this->applicationVersion);
            }

            $statusCode = $compiledCommand->execute($response);

            if ($statusCode === null) {
                return StatusCodes::OK;
            }

            return $statusCode;
        } catch (InvalidArgumentException $ex) {
            $response->writeln("<error>{$ex->getMessage()}</error>");

            return StatusCodes::ERROR;
        } catch (Exception $ex) {
            $response->writeln("<fatal>{$ex->getMessage()}</fatal>");

            return StatusCodes::FATAL;
        } catch (Throwable $ex) {
            $response->writeln("<fatal>{$ex->getMessage()}</fatal>");

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
    private function getCompiledHelpCommand(IRequest $request) : ICommand
    {
        $helpCommand = new HelpCommand(new CommandFormatter(), new PaddingFormatter());
        $commandName = null;

        if ($request->getCommandName() === 'help') {
            $compiledHelpCommand = $this->commandCompiler->compile($helpCommand, $request);

            if ($compiledHelpCommand->argumentValueIsSet('command')) {
                $commandName = $compiledHelpCommand->getArgumentValue('command');
            }
        } else {
            $commandName = $request->getCommandName();
        }

        // Set the command only if it was passed as an argument to the help command
        if ($commandName !== null && $commandName !== '') {
            if (!$this->commandCollection->has($commandName)) {
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
    private function isInvokingHelpCommand(IRequest $request) : bool
    {
        return $request->getCommandName() === 'help' || $request->optionIsSet('h') || $request->optionIsSet('help');
    }

    /**
     * Gets whether or not the input is invoking the version command
     *
     * @param IRequest $request The parsed request
     * @return bool True if it is invoking the version command, otherwise false
     */
    private function isInvokingVersionCommand(IRequest $request) : bool
    {
        return $request->optionIsSet('v') || $request->optionIsSet('version');
    }
}
