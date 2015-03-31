<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the command bootstrapper
 */
namespace RDev\Framework\Bootstrappers\Console\Commands;
use RDev\Applications\Bootstrappers\Bootstrapper;
use RDev\Console\Commands\CommandCollection;
use RDev\Console\Commands\Compilers\Compiler;
use RDev\Console\Commands\Compilers\ICompiler;
use RDev\IoC\IContainer;

class Commands extends Bootstrapper
{
    /** @var array The list of built-in command classes */
    private static $commandClasses = [
        "RDev\\Framework\\Console\\Commands\\AppEnvironmentCommand",
        "RDev\\Framework\\Console\\Commands\\ComposerDumpAutoloadCommand",
        "RDev\\Framework\\Console\\Commands\\ComposerUpdateCommand",
        "RDev\\Framework\\Console\\Commands\\EncryptionKeyGenerationCommand",
        "RDev\\Framework\\Console\\Commands\\FlushViewCacheCommand",
        "RDev\\Framework\\Console\\Commands\\MakeCommandCommand",
        "RDev\\Framework\\Console\\Commands\\MakeControllerCommand",
        "RDev\\Framework\\Console\\Commands\\MakeDataMapperCommand",
        "RDev\\Framework\\Console\\Commands\\MakeEntityCommand",
        "RDev\\Framework\\Console\\Commands\\MakeHTTPMiddlewareCommand",
        "RDev\\Framework\\Console\\Commands\\RenameAppCommand"
    ];
    /** @var CommandCollection The list of console commands */
    private $commandCollection = null;

    /**
     * {@inheritdoc}
     */
    public function registerBindings(IContainer $container)
    {
        $compiler = $this->getCommandCompiler($container);
        $container->bind("RDev\\Console\\Commands\\Compilers\\ICompiler", $compiler);
        $this->commandCollection = new CommandCollection($compiler);
        $container->bind("RDev\\Console\\Commands\\CommandCollection", $this->commandCollection);
    }

    /**
     * Adds built-in commands to our list
     *
     * @param IContainer $container The dependency injection container to use
     */
    public function run(IContainer $container)
    {
        // Instantiate each command class
        foreach(self::$commandClasses as $commandClass)
        {
            $this->commandCollection->add($container->makeShared($commandClass));
        }
    }

    /**
     * Gets the command compiler
     * To use a different command compiler than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return ICompiler The command compiler
     */
    protected function getCommandCompiler(IContainer $container)
    {
        return new Compiler();
    }
}