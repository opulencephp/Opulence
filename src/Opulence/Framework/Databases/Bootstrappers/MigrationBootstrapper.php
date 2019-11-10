<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Databases\Bootstrappers;

use Opulence\Databases\Adapters\Pdo\MySql\Driver as MySqlDriver;
use Opulence\Databases\Adapters\Pdo\PostgreSql\Driver as PostgreSqlDriver;
use Opulence\Databases\IConnection;
use Opulence\Databases\Migrations\FileMigrationFinder;
use Opulence\Databases\Migrations\IExecutedMigrationRepository;
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
    /** @var BaseQueryBuilder|null */
    protected $queryBuilder;

    /** @var array|null */
    protected $allMigrationClasses;

    /**
     * @inheritdoc
     */
    public function getBindings() : array
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
        $this->registerMigrator($container);
        $this->registerFixMigrationsCommand($container);
    }

    /**
     * @param IContainer $container
     */
    protected function registerMigrator(IContainer $container)
    {
        $container->bindFactory(IMigrator::class, function () use ($container) {
            return new Migrator(
                $this->getAllMigrationClasses(),
                $container->resolve(IConnection::class),
                new ContainerMigrationResolver($container),
                $this->getExecutedMigrationRepository($container)
            );
        });
    }

    /**
     * @param IContainer $container
     */
    protected function registerFixMigrationsCommand(IContainer $container)
    {
        $container->bindFactory(FixMigrationsCommand::class, function () use ($container) {
            return new FixMigrationsCommand(
                SqlExecutedMigrationRepository::DEFAULT_TABLE_NAME,
                $this->getAllMigrationClasses(),
                $container->resolve(IConnection::class),
                $this->bindQueryBuilder($container)
            );
        });
    }

    /**
     * @return array
     */
    protected function getAllMigrationClasses(): array
    {
        if (null !== $this->allMigrationClasses) {
            return $this->allMigrationClasses;
        }

        $this->allMigrationClasses = (new FileMigrationFinder)->findAll(Config::get('paths', 'database.migrations'));

        return $this->allMigrationClasses;
    }

    /**
     * @param IContainer $container
     *
     * @return BaseQueryBuilder
     */
    protected function bindQueryBuilder(IContainer $container) : BaseQueryBuilder
    {
        if (null !== $this->queryBuilder) {
            return $this->queryBuilder;
        }

        $driverClass = getenv('DB_DRIVER') ?: PostgreSqlDriver::class;

        switch ($driverClass) {
            case MySqlDriver::class:
                $this->queryBuilder = new MySqlQueryBuilder();
                $container->bindInstance(MySqlQueryBuilder::class, $this->queryBuilder);
                break;
            case PostgreSqlDriver::class:
                $this->queryBuilder = new PostgreSqlQueryBuilder();
                $container->bindInstance(PostgreSqlQueryBuilder::class, $this->queryBuilder);
                break;
            default:
                throw new RuntimeException(
                    "Invalid database driver type specified in environment var \"DB_DRIVER\": $driverClass"
                );
        }

        $container->bindInstance(BaseQueryBuilder::class, $this->queryBuilder);

        return $this->queryBuilder;
    }

    /**
     * Gets the executed migration repository to use in the migrator
     *
     * @param IContainer $container The IoC container
     * @return IExecutedMigrationRepository The executed migration repository
     * @throws RuntimeException Thrown if there was an error resolving the query builder
     */
    protected function getExecutedMigrationRepository(IContainer $container) : IExecutedMigrationRepository
    {
        $queryBuilder = $this->bindQueryBuilder($container);

        return new SqlExecutedMigrationRepository(
            SqlExecutedMigrationRepository::DEFAULT_TABLE_NAME,
            $container->resolve(IConnection::class),
            $queryBuilder,
            new TypeMapperFactory()
        );
    }
}
