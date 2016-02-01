<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Bootstrappers\Console\Commands;

use Opulence\Bootstrappers\Bootstrapper;
use Opulence\Console\Commands\CommandCollection;
use Opulence\Console\Commands\Compilers\Compiler;
use Opulence\Console\Commands\Compilers\ICompiler;
use Opulence\Framework\Console\Commands\AppDownCommand;
use Opulence\Framework\Console\Commands\AppEnvironmentCommand;
use Opulence\Framework\Console\Commands\AppUpCommand;
use Opulence\Framework\Console\Commands\ComposerDumpAutoloadCommand;
use Opulence\Framework\Console\Commands\ComposerUpdateCommand;
use Opulence\Framework\Console\Commands\EncryptionKeyGenerationCommand;
use Opulence\Framework\Console\Commands\FlushFrameworkCacheCommand;
use Opulence\Framework\Console\Commands\FlushViewCacheCommand;
use Opulence\Framework\Console\Commands\MakeCommandCommand;
use Opulence\Framework\Console\Commands\MakeControllerCommand;
use Opulence\Framework\Console\Commands\MakeDataMapperCommand;
use Opulence\Framework\Console\Commands\MakeEntityCommand;
use Opulence\Framework\Console\Commands\MakeHttpMiddlewareCommand;
use Opulence\Framework\Console\Commands\RenameAppCommand;
use Opulence\Ioc\IContainer;

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
        RenameAppCommand::class
    ];
    /** @var CommandCollection The list of console commands */
    private $commandCollection = null;

    /**
     * @inheritdoc
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
        foreach (self::$commandClasses as $commandClass) {
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
    protected function getCommandCompiler(IContainer $container) : ICompiler
    {
        return new Compiler();
    }
}