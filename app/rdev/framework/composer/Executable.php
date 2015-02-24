<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a wrapper for the Composer executable
 */
namespace RDev\Framework\Composer;
use RDev\Applications;

class Executable
{
    /** @var string The executable */
    private $executable = "";

    /**
     * @param Applications\Paths $paths The paths of the application
     */
    public function __construct(Applications\Paths $paths)
    {
        if(file_exists($paths["root"] . "/composer.phar"))
        {
            $this->executable = '"' . PHP_BINARY . '" composer.phar';
        }
        else
        {
            $this->executable = "composer";
        }
    }

    /**
     * Performs a dump-autoload
     *
     * @param string $options The options to run
     * @return string The output of the autoload
     */
    public function dumpAutoload($options = "")
    {
        return $this->execute("{$this->executable} dump-autoload $options");
    }

    /**
     * Performs an update
     *
     * @param string $options The options to run
     * @return string The output of the update
     */
    public function update($options = "")
    {
        return $this->execute("{$this->executable} update $options");
    }

    /**
     * Executes a command
     *
     * @param string $command The command to execute
     * @return string The output of the command
     */
    protected function execute($command)
    {
        return shell_exec($command);
    }
}