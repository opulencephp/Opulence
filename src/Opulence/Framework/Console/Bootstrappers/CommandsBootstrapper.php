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
use Opulence\Framework\Cryptography\Console\Commands\EncryptionKeyGenerationCommand;
use Opulence\Framework\Cryptography\Console\Commands\UuidGenerationCommand;
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
        FlushFrameworkCacheCommand::class,
        FlushViewCacheCommand::class,
        MakeCommandCommand::class,
        MakeControllerCommand::class,
        MakeDataMapperCommand::class,
        MakeEntityCommand::class,
        MakeHttpMiddlewareCommand::class,
        RenameAppCommand::class,
        UuidGenerationCommand::class
    ];
    /** @var CommandCollection The list of console commands */
    private $commandCollection = null;

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $compiler = $this->getCommandCompiler($container);
        $container->bindInstance(ICompiler::class, $compiler);
        $this->commandCollection = new CommandCollection($compiler);
        $container->bindInstance(CommandCollection::class, $this->commandCollection);
        $container->bindFactory(FlushFrameworkCacheCommand::class, function () use ($container) {
            try {
                return new FlushFrameworkCacheCommand(
                    new FileCache(Config::get('paths', 'tmp.framework.http') . '/cachedBootstrapperRegistry.json'),
                    new FileCache(Config::get('paths', 'tmp.framework.console') . '/cachedBootstrapperRegistry.json'),
                    $container->resolve(RouteCache::class),
                    $container->resolve(ViewCache::class)
                );
            } catch (IocException $ex) {
                throw new RuntimeException('Failed to bind factory for ' . FlushFrameworkCacheCommand::class, $ex);
            }
        });
    }

    /**
     * Adds built-in commands to our list
     *
     * @param IContainer $container The dependency injection container to use
     */
    public function run(IContainer $container)
    {
        // Instantiate each command class
        foreach (self::$commandClasses as $commandClass) {
            $this->commandCollection->add($container->resolve($commandClass));
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
