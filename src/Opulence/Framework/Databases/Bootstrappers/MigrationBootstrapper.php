<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Databases\Bootstrappers;

use Aphiria\DependencyInjection\Bootstrappers\Bootstrapper;
use Aphiria\DependencyInjection\IContainer;
use Opulence\Databases\Adapters\Pdo\MySql\Driver as MySqlDriver;
use Opulence\Databases\Adapters\Pdo\PostgreSql\Driver as PostgreSqlDriver;
use Opulence\Databases\IConnection;
use Opulence\Databases\Migrations\FileMigrationFinder;
use Opulence\Databases\Migrations\IExecutedMigrationRepository;
use Opulence\Databases\Migrations\IMigrator;
use Opulence\Databases\Migrations\Migrator;
use Opulence\Databases\Providers\Types\Factories\TypeMapperFactory;
use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Databases\Migrations\ContainerMigrationResolver;
use Opulence\Framework\Databases\Migrations\SqlExecutedMigrationRepository;
use Opulence\QueryBuilders\MySql\QueryBuilder as MySqlQueryBuilder;
use Opulence\QueryBuilders\PostgreSql\QueryBuilder as PostgreSqlQueryBuilder;
use Opulence\QueryBuilders\QueryBuilder as BaseQueryBuilder;
use RuntimeException;

/**
 * Defines the database migration bootstrapper
 */
final class MigrationBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container): void
    {
        $container->bindFactory(IMigrator::class, function () use ($container) {
            return new Migrator(
                (new FileMigrationFinder)->findAll(Config::get('paths', 'database.migrations')),
                $container->resolve(IConnection::class),
                new ContainerMigrationResolver($container),
                $this->getExecutedMigrationRepository($container)
            );
        });
    }

    /**
     * Gets the executed migration repository to use in the migrator
     *
     * @param IContainer $container The IoC container
     * @return IExecutedMigrationRepository The executed migration repository
     * @throws RuntimeException Thrown if there was an error resolving the query builder
     */
    protected function getExecutedMigrationRepository(IContainer $container): IExecutedMigrationRepository
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

        return new SqlExecutedMigrationRepository(
            SqlExecutedMigrationRepository::DEFAULT_TABLE_NAME,
            $container->resolve(IConnection::class),
            $queryBuilder,
            new TypeMapperFactory()
        );
    }
}
