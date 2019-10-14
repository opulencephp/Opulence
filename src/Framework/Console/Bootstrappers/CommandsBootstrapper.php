<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Console\Bootstrappers;

use Aphiria\Console\Commands\CommandRegistry;
use Aphiria\DependencyInjection\Bootstrappers\Bootstrapper;
use Aphiria\DependencyInjection\IContainer;
use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Console\ClassFileCompiler;
use Opulence\Framework\Console\Commands\AppDownCommand;
use Opulence\Framework\Console\Commands\AppDownCommandHandler;
use Opulence\Framework\Console\Commands\AppEnvironmentCommand;
use Opulence\Framework\Console\Commands\AppEnvironmentCommandHandler;
use Opulence\Framework\Console\Commands\AppUpCommand;
use Opulence\Framework\Console\Commands\AppUpCommandHandler;
use Opulence\Framework\Console\Commands\FlushFrameworkCacheCommand;
use Opulence\Framework\Console\Commands\FlushFrameworkCacheCommandHandler;
use Opulence\Framework\Console\Commands\RunAppLocallyCommand;
use Opulence\Framework\Console\Commands\RunAppLocallyCommandHandler;
use Opulence\Framework\Cryptography\Console\Commands\EncryptionKeyGenerationCommand;
use Opulence\Framework\Cryptography\Console\Commands\EncryptionKeyGenerationCommandHandler;
use Opulence\Framework\Cryptography\Console\Commands\UuidGenerationCommand;
use Opulence\Framework\Cryptography\Console\Commands\UuidGenerationCommandHandler;
use Opulence\Framework\Databases\Console\Commands\MakeMigrationCommand;
use Opulence\Framework\Databases\Console\Commands\MakeMigrationCommandHandler;
use Opulence\Framework\Databases\Console\Commands\RunDownMigrationsCommand;
use Opulence\Framework\Databases\Console\Commands\RunDownMigrationsCommandHandler;
use Opulence\Framework\Databases\Console\Commands\RunUpMigrationsCommand;
use Opulence\Framework\Databases\Console\Commands\RunUpMigrationsCommandHandler;
use Opulence\Framework\Orm\Console\Commands\MakeDataMapperCommand;
use Opulence\Framework\Orm\Console\Commands\MakeDataMapperCommandHandler;
use Opulence\Framework\Orm\Console\Commands\MakeEntityCommand;
use Opulence\Framework\Orm\Console\Commands\MakeEntityCommandHandler;
use Opulence\Framework\Views\Console\Commands\FlushViewCacheCommand;
use Opulence\Framework\Views\Console\Commands\FlushViewCacheCommandHandler;

/**
 * Defines the command bootstrapper
 */
final class CommandsBootstrapper extends Bootstrapper
{
    /** @var array The list of built-in command classes */
    private static array $commandClasses = [
        AppDownCommand::class => AppDownCommandHandler::class,
        AppEnvironmentCommand::class => AppEnvironmentCommandHandler::class,
        AppUpCommand::class => AppUpCommandHandler::class,
        EncryptionKeyGenerationCommand::class => EncryptionKeyGenerationCommandHandler::class,
        FlushFrameworkCacheCommand::class => FlushFrameworkCacheCommandHandler::class,
        FlushViewCacheCommand::class => FlushViewCacheCommandHandler::class,
        MakeMigrationCommand::class => MakeMigrationCommandHandler::class,
        MakeDataMapperCommand::class => MakeDataMapperCommandHandler::class,
        MakeEntityCommand::class => MakeEntityCommandHandler::class,
        RunDownMigrationsCommand::class => RunDownMigrationsCommandHandler::class,
        RunUpMigrationsCommand::class => RunUpMigrationsCommandHandler::class,
        UuidGenerationCommand::class => UuidGenerationCommandHandler::class
    ];

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        $container->bindInstance(CommandRegistry::class, $commands = new CommandRegistry());
        $container->bindInstance(ClassFileCompiler::class, new ClassFileCompiler(Config::get('paths', 'root') . '/composer.json'));
        $this->registerCommands($commands, $container);
    }

    /**
     * Registers commands with the application
     *
     * @param CommandRegistry $commands The commands to register to
     * @param IContainer $container The dependency injection container to use
     */
    protected function registerCommands(CommandRegistry $commands, IContainer $container): void
    {
        // Resolve and add each command class
        foreach (self::$commandClasses as $commandClass => $commandHandlerClass) {
            // We are presuming that the commands' constructors are parameterless
            $commands->registerCommand(new $commandClass(), fn () => $container->resolve($commandHandlerClass));
        }

        // The command to run the app locally requires a path to the router file
        $commands->registerCommand(
            new RunAppLocallyCommand(Config::get('paths', 'root') . '/localhost_router.php'),
            fn () => $container->resolve(RunAppLocallyCommandHandler::class)
        );
    }
}
