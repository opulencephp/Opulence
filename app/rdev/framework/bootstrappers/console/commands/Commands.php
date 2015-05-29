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
use RDev\Framework\Console\Commands\AppEnvironmentCommand;
use RDev\Framework\Console\Commands\ComposerDumpAutoloadCommand;
use RDev\Framework\Console\Commands\ComposerUpdateCommand;
use RDev\Framework\Console\Commands\EncryptionKeyGenerationCommand;
use RDev\Framework\Console\Commands\FlushFrameworkCacheCommand;
use RDev\Framework\Console\Commands\FlushViewCacheCommand;
use RDev\Framework\Console\Commands\MakeCommandCommand;
use RDev\Framework\Console\Commands\MakeControllerCommand;
use RDev\Framework\Console\Commands\MakeDataMapperCommand;
use RDev\Framework\Console\Commands\MakeEntityCommand;
use RDev\Framework\Console\Commands\MakeHTTPMiddlewareCommand;
use RDev\Framework\Console\Commands\RenameAppCommand;
use RDev\IoC\IContainer;

class Commands extends Bootstrapper
{
    /** @var array The list of built-in command classes */
    private static $commandClasses = [
        AppEnvironmentCommand::class,
        ComposerDumpAutoloadCommand::class,
        ComposerUpdateCommand::class,
        EncryptionKeyGenerationCommand::class,
        FlushFrameworkCacheCommand::class,
        FlushViewCacheCommand::class,
        MakeCommandCommand::class,
        MakeControllerCommand::class,
        MakeDataMapperCommand::class,
        MakeEntityCommand::class,
        MakeHTTPMiddlewareCommand::class,
        RenameAppCommand::class
    ];
    /** @var CommandCollection The list of console commands */
    private $commandCollection = null;

    /**
     * {@inheritdoc}
     */
    public function registerBindings(IContainer $container)
    {
        $compiler = $this->getCommandCompiler($container);
        $container->bind(ICompiler::class, $compiler);
        $this->commandCollection = new CommandCollection($compiler);
        $container->bind(CommandCollection::class, $this->commandCollection);
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