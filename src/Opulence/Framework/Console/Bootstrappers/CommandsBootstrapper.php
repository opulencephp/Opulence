<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Console\Bootstrappers;

use Opulence\Console\Commands\CommandCollection;
use Opulence\Console\Commands\Compilers\Compiler;
use Opulence\Console\Commands\Compilers\ICompiler;
use Opulence\Framework\Composer\Console\Commands\ComposerDumpAutoloadCommand;
use Opulence\Framework\Composer\Console\Commands\ComposerUpdateCommand;
use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Console\Commands\AppDownCommand;
use Opulence\Framework\Console\Commands\AppEnvironmentCommand;
use Opulence\Framework\Console\Commands\AppUpCommand;
use Opulence\Framework\Console\Commands\FlushFrameworkCacheCommand;
use Opulence\Framework\Console\Commands\MakeCommandCommand;
use Opulence\Framework\Console\Commands\RenameAppCommand;
use Opulence\Framework\Console\Commands\RunAppLocallyCommand;
use Opulence\Framework\Cryptography\Console\Commands\EncryptionKeyGenerationCommand;
use Opulence\Framework\Cryptography\Console\Commands\UuidGenerationCommand;
use Opulence\Framework\Databases\Console\Commands\MakeMigrationCommand;
use Opulence\Framework\Databases\Console\Commands\RunDownMigrationsCommand;
use Opulence\Framework\Databases\Console\Commands\RunUpMigrationsCommand;
use Opulence\Framework\Orm\Console\Commands\MakeDataMapperCommand;
use Opulence\Framework\Orm\Console\Commands\MakeEntityCommand;
use Opulence\Framework\Routing\Console\Commands\MakeControllerCommand;
use Opulence\Framework\Routing\Console\Commands\MakeHttpMiddlewareCommand;
use Opulence\Framework\Views\Console\Commands\FlushViewCacheCommand;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\Caching\FileCache;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;
use Opulence\Routing\Routes\Caching\ICache as RouteCache;
use Opulence\Views\Caching\ICache as ViewCache;
use RuntimeException;

/**
 * Defines the command bootstrapper
 */
class CommandsBootstrapper extends Bootstrapper
{
    /** @var array The list of built-in command classes */
    private static $commandClasses = [
        AppDownCommand::class,
        AppEnvironmentCommand::class,
        AppUpCommand::class,
        ComposerDumpAutoloadCommand::class,
        ComposerUpdateCommand::class,
        EncryptionKeyGenerationCommand::class,
        FlushViewCacheCommand::class,
        MakeCommandCommand::class,
        MakeControllerCommand::class,
        MakeMigrationCommand::class,
        MakeDataMapperCommand::class,
        MakeEntityCommand::class,
        MakeHttpMiddlewareCommand::class,
        RenameAppCommand::class,
        RunAppLocallyCommand::class,
        RunDownMigrationsCommand::class,
        RunUpMigrationsCommand::class,
        UuidGenerationCommand::class
    ];

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        // Use a factory to defer the resolution of the commands
        // The commands may have dependencies set in other bootstrappers
        $container->bindFactory(
            CommandCollection::class,
            function () use ($container) {
                $compiler = $this->getCommandCompiler($container);
                $container->bindInstance(ICompiler::class, $compiler);
                $commands = new CommandCollection($compiler);
                $this->bindCommands($commands, $container);

                return $commands;
            },
            true
        );
    }

    /**
     * Binds commands to the collection
     *
     * @param CommandCollection $commands The collection to add commands to
     * @param IContainer $container The dependency injection container to use
     */
    protected function bindCommands(CommandCollection $commands, IContainer $container)
    {
        // Resolve and add each command class
        foreach (self::$commandClasses as $commandClass) {
            $commands->add($container->resolve($commandClass));
        }

        // The flush-cache command requires some special configuration
        try {
            $flushCacheCommand = new FlushFrameworkCacheCommand(
                new FileCache(Config::get('paths', 'tmp.framework.http') . '/cachedBootstrapperRegistry.json'),
                new FileCache(Config::get('paths', 'tmp.framework.console') . '/cachedBootstrapperRegistry.json'),
                $container->resolve(RouteCache::class),
                $container->resolve(ViewCache::class)
            );
            $commands->add($flushCacheCommand);
        } catch (IocException $ex) {
            throw new RuntimeException('Failed to resolve ' . FlushFrameworkCacheCommand::class, 0, $ex);
        }
    }

    /**
     * Gets the command compiler
     * To use a different command compiler than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return ICompiler The command compiler
     */
    protected function getCommandCompiler(IContainer $container) : ICompiler
    {
        return new Compiler();
    }
}
