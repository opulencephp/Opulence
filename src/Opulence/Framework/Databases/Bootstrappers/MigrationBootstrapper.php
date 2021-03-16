<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Framework\Databases\Bootstrappers;

use Opulence\Databases\Adapters\Pdo\MySql\Driver as MySqlDriver;
use Opulence\Databases\Adapters\Pdo\PostgreSql\Driver as PostgreSqlDriver;
use Opulence\Databases\IConnection;
use Opulence\Databases\Migrations\FileMigrationFinder;
use Opulence\Databases\Migrations\IMigrator;
use Opulence\Databases\Migrations\Migrator;
use Opulence\Databases\Providers\Types\Factories\TypeMapperFactory;
use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Databases\Console\Commands\FixMigrationsCommand;
use Opulence\Framework\Databases\Migrations\ContainerMigrationResolver;
use Opulence\Framework\Databases\Migrations\SqlExecutedMigrationRepository;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\QueryBuilders\MySql\QueryBuilder as MySqlQueryBuilder;
use Opulence\QueryBuilders\PostgreSql\QueryBuilder as PostgreSqlQueryBuilder;
use Opulence\QueryBuilders\QueryBuilder as BaseQueryBuilder;
use RuntimeException;

/**
 * Defines the database migration bootstrapper
 */
class MigrationBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [
            IMigrator::class,
            FixMigrationsCommand::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $allMigrationClasses = (new FileMigrationFinder())->findAll(Config::get('paths', 'database.migrations'));
        $queryBuilder = $this->registerQueryBuilder($container);

        $this->registerMigrator($container, $queryBuilder, $allMigrationClasses);
        $this->registerFixMigrationsCommand($container, $queryBuilder, $allMigrationClasses);
    }

    /**
     * @param IContainer $container
     * @param BaseQueryBuilder $queryBuilder
     * @param array $allMigrationClasses
     */
    protected function registerMigrator(
        IContainer $container,
        BaseQueryBuilder $queryBuilder,
        array $allMigrationClasses
    ) {
        $executeMigrationRepo = new SqlExecutedMigrationRepository(
            SqlExecutedMigrationRepository::DEFAULT_TABLE_NAME,
            $container->resolve(IConnection::class),
            $queryBuilder,
            new TypeMapperFactory()
        );

        $container->bindFactory(
            IMigrator::class,
            function () use ($container, $queryBuilder, $executeMigrationRepo, $allMigrationClasses) {
                return new Migrator(
                    $allMigrationClasses,
                    $container->resolve(IConnection::class),
                    new ContainerMigrationResolver($container),
                    $executeMigrationRepo
                );
            }
        );
    }

    /**
     * @param IContainer $container
     * @param BaseQueryBuilder $queryBuilder
     * @param array $allMigrationClasses
     */
    protected function registerFixMigrationsCommand(
        IContainer $container,
        BaseQueryBuilder $queryBuilder,
        array $allMigrationClasses
    ) {
        $container->bindFactory(
            FixMigrationsCommand::class,
            function () use ($container, $queryBuilder, $allMigrationClasses) {
                return new FixMigrationsCommand(
                    SqlExecutedMigrationRepository::DEFAULT_TABLE_NAME,
                    $allMigrationClasses,
                    $container->resolve(IConnection::class),
                    $queryBuilder
                );
            }
        );
    }

    /**
     * @param IContainer $container
     *
     * @return BaseQueryBuilder
     */
    protected function registerQueryBuilder(IContainer $container): BaseQueryBuilder
    {
        $driverClass = getenv('DB_DRIVER') ?: PostgreSqlDriver::class;

        switch ($driverClass) {
            case MySqlDriver::class:
                $queryBuilder = new MySqlQueryBuilder();
                $container->bindInstance(MySqlQueryBuilder::class, $queryBuilder);
                break;
            case PostgreSqlDriver::class:
                $queryBuilder = new PostgreSqlQueryBuilder();
                $container->bindInstance(PostgreSqlQueryBuilder::class, $queryBuilder);
                break;
            default:
                throw new RuntimeException(
                    "Invalid database driver type specified in environment var \"DB_DRIVER\": $driverClass"
                );
        }

        $container->bindInstance(BaseQueryBuilder::class, $queryBuilder);

        return $queryBuilder;
    }
}
