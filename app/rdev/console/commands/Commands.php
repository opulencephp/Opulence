<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the console commands container
 */
namespace RDev\Console\Commands;

class Commands
{
    /** @var ICommand[] The list of commands */
    private $commands = [];

    /**
     * Adds a command
     *
     * @param string $name The name of the command as it will be typed into the console
     * @param ICommand $command The command to add
     * @param string $description A brief description of the command
     * @throws \InvalidArgumentException Thrown if a command with the input name already exists
     */
    public function add($name, ICommand $command, $description)
    {
        if($this->has($name))
        {
            throw new \InvalidArgumentException("A command with name \"$name\" already exists");
        }

        $this->commands[$name] = [
            "command" => $command,
            "description" => $description
        ];
    }

    /**
     * Gets the command with the input name
     *
     * @param string $name The name of the command to get
     * @return ICommand The command
     * @throws \InvalidArgumentException Thrown if no command with the input name exists
     */
    public function get($name)
    {
        if(!$this->has($name))
        {
            throw new \InvalidArgumentException("No command with name \"$name\" exists");
        }

        return $this->commands[$name]["command"];
    }

    /**
     * Gets all the commands
     *
     * @return array The list of arrays with three keys:
     *      "name" => The name of the command,
     *      "command" => The command object,
     *      "description" => The description
     */
    public function getAll()
    {
        $returnData = [];

        foreach($this->commands as $name => $commandData)
        {
            $returnData[] = [
                "name" => $name,
                "command" => $commandData["command"],
                "description" => $commandData["description"]
            ];
        }

        return $returnData;
    }

    /**
     * Gets the description of the command with the input name
     *
     * @param string $name The name of the command to get
     * @return string The description of the command if it was found, otherwise an empty string
     */
    public function getDescription($name)
    {
        if(!$this->has($name))
        {
            return "";
        }

        return $this->commands[$name]["description"];
    }

    /**
     * Checks if the input name has been added
     *
     * @param string $name The name of the command to look for
     * @return bool True if the command has been added, otherwise false
     */
    public function has($name)
    {
        return isset($this->commands[$name]);
    }
}