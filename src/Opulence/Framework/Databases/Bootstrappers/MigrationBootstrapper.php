<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Databases\Bootstrappers;

use Opulence\Databases\IConnection;
use Opulence\Databases\Migrations\FileMigrationFinder;
use Opulence\Databases\Migrations\IExecutedMigrationRepository;
use Opulence\Databases\Migrations\IMigrator;
use Opulence\Databases\Migrations\Migrator;
use Opulence\Databases\Providers\Types\Factories\TypeMapperFactory;
use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Databases\Migrations\ContainerMigrationResolver;
use Opulence\Framework\Databases\Migrations\SqlExecutedMigrationRepository;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\QueryBuilders\PostgreSql\QueryBuilder;

/**
 * Defines the database migration bootstrapper
 */
class MigrationBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings() : array
    {
        return [IMigrator::class];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
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
     */
    protected function getExecutedMigrationRepository(IContainer $container) : IExecutedMigrationRepository
    {
        return new SqlExecutedMigrationRepository(
            SqlExecutedMigrationRepository::DEFAULT_TABLE_NAME,
            $container->resolve(IConnection::class),
            new QueryBuilder(),
            new TypeMapperFactory()
        );
    }
}
