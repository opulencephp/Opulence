<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Console\Commands;

use InvalidArgumentException;
use Opulence\Console\Commands\Compilers\ICompiler;
use Opulence\Console\Requests\Parsers\ArrayListParser;
use Opulence\Console\Responses\IResponse;

/**
 * Defines the console commands container
 */
class CommandCollection
{
    /** @var ICommand[] The list of commands */
    private $commands = [];
    /** @var ICompiler The command compiler */
    private $commandCompiler = null;
    /** @var ArrayListParser The request parser */
    private $requestParser = null;

    /**
     * @param ICompiler $commandCompiler The command compiler
     */
    public function __construct(ICompiler $commandCompiler)
    {
        $this->commandCompiler = $commandCompiler;
        $this->requestParser = new ArrayListParser();
    }

    /**
     * Adds a command
     *
     * @param ICommand $command The command to add
     * @param bool $overwrite True if we will overwrite a command with the same name if it already exists
     * @throws InvalidArgumentException Thrown if a command with the input name already exists
     */
    public function add(ICommand $command, bool $overwrite = false) : void
    {
        if (!$overwrite && $this->has($command->getName())) {
            throw new InvalidArgumentException("A command with name \"{$command->getName()}\" already exists");
        }

        $command->setCommandCollection($this);
        $this->commands[$command->getName()] = $command;
    }

    /**
     * Calls a command and writes its output to the input response
     *
     * @param string $commandName The name of the command to run
     * @param IResponse $response The response to write output to
     * @param array $arguments The list of arguments
     * @param array $options The list of options
     * @return int|null The status code of the command
     * @throws InvalidArgumentException Thrown if no command exists with the input name
     */
    public function call(string $commandName, IResponse $response, array $arguments = [], array $options = []) : ?int
    {
        $request = $this->requestParser->parse([
            'name' => $commandName,
            'arguments' => $arguments,
            'options' => $options
        ]);
        $compiledCommand = $this->commandCompiler->compile($this->get($commandName), $request);

        return $compiledCommand->execute($response);
    }

    /**
     * Gets the command with the input name
     *
     * @param string $name The name of the command to get
     * @return ICommand The command
     * @throws InvalidArgumentException Thrown if no command with the input name exists
     */
    public function get(string $name) : ICommand
    {
        if (!$this->has($name)) {
            throw new InvalidArgumentException("No command with name \"$name\" exists");
        }

        return $this->commands[$name];
    }

    /**
     * Gets all the commands
     *
     * @return ICommand[] The list of commands
     */
    public function getAll() : array
    {
        return array_values($this->commands);
    }

    /**
     * Checks if the input name has been added
     *
     * @param string $name The name of the command to look for
     * @return bool True if the command has been added, otherwise false
     */
    public function has(string $name) : bool
    {
        return isset($this->commands[$name]);
    }
}
