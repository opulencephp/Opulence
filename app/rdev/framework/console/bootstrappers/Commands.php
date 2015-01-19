<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the command bootstrapper
 */
namespace RDev\Framework\Console\Bootstrappers;
use RDev\Applications\Bootstrappers;
use RDev\Console\Commands as ConsoleCommands;
use RDev\Console\Commands\Compilers;
use RDev\IoC;

class Commands implements Bootstrappers\IBootstrapper
{
    /** @var array The list of built-in command classes */
    private static $commandClasses = [
        "RDev\\Framework\\Console\\Commands\\FlushViewCache"
    ];
    /** @var IoC\IContainer The dependency injection container to use */
    private $container = null;

    /**
     * @param IoC\IContainer $container The dependency injection container to use
     */
    public function __construct(IoC\IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $compiler = new Compilers\Compiler();
        $this->container->bind("RDev\\Console\\Commands\\Compilers\\ICompiler", $compiler);
        $commands = new ConsoleCommands\Commands();

        // Instantiate each command class
        foreach(self::$commandClasses as $commandClass)
        {
            $commands->add($this->container->makeShared($commandClass));
        }

        $this->container->bind("RDev\\Console\\Commands\\Commands", $commands);
    }
}