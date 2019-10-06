<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Databases\Migrations;

use Exception;
use Opulence\Databases\IConnection;

/**
 * Defines the database migrator
 */
class Migrator implements IMigrator
{
    /** @var string[] The list of migration classes */
    private $allMigrationClasses = [];
    /** @var IConnection The connection to use in the migrations */
    private $connection = null;
    /** @var IMigrationResolver The migration resolver */
    private $migrationResolver = null;
    /** @var IExecutedMigrationRepository The executed migration repository */
    private $executedMigrations = null;

    /**
     * @param string[] $allMigrationClasses The list of migration classes
     * @param IConnection $connection The connection to use in the migrations
     * @param IMigrationResolver $migrationResolver The migration resolver
     * @param IExecutedMigrationRepository $executedMigrations The executed migration repository
     */
    public function __construct(
        array $allMigrationClasses,
        IConnection $connection,
        IMigrationResolver $migrationResolver,
        IExecutedMigrationRepository $executedMigrations
    ) {
        $this->allMigrationClasses = $allMigrationClasses;
        $this->connection = $connection;
        $this->migrationResolver = $migrationResolver;
        $this->executedMigrations = $executedMigrations;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function rollBackAllMigrations() : array
    {
        // These classes are returned in chronologically descending order
        $migrationClasses = $this->executedMigrations->getAll();
        $migrations = $this->resolveManyMigrations($migrationClasses);
        try {
            $this->executeRollBacks($migrations);
        } catch (Exception $e) {
            $this->connection->rollBack();

            throw $e;
        }

        return $migrationClasses;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function rollBackMigrations(int $number = 1) : array
    {
        // These classes are returned in chronologically descending order
        $migrationClasses = $this->executedMigrations->getLast($number);
        $migrations = $this->resolveManyMigrations($migrationClasses);
        try {
            $this->executeRollBacks($migrations);
        } catch (Exception $e) {
            $this->connection->rollBack();

            throw $e;
        }

        return $migrationClasses;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function runMigrations() : array
    {
        $runMigrationClasses = $this->executedMigrations->getAll();
        // We want to reset the array keys, which is why we grab the values
        $migrationClassesToRun = array_values(array_diff($this->allMigrationClasses, $runMigrationClasses));
        $migrations = $this->resolveManyMigrations($migrationClassesToRun);
        $this->connection->beginTransaction();

        foreach ($migrations as $migration) {
            try {
                $migration->up();
            } catch (Exception $e) {
                $this->connection->rollBack();

                throw $e;
            }

            $this->executedMigrations->add(get_class($migration));
        }

        $this->connection->commit();

        return $migrationClassesToRun;
    }

    /**
     * Executes the roll backs on a list of migrations
     *
     * @param IMigration[] $migrations The migrations to execute the down method on
     */
    private function executeRollBacks(array $migrations) : void
    {
        $this->connection->beginTransaction();

        foreach ($migrations as $migration) {
            $migration->down();

            $this->executedMigrations->delete(get_class($migration));
        }

        $this->connection->commit();
    }

    /**
     * Resolves many migrations at once
     *
     * @param string[] $migrationClasses The list of migration classes to resolve
     * @return IMigration[] The list of resolved migrations
     */
    private function resolveManyMigrations(array $migrationClasses) : array
    {
        $migrations = [];

        foreach ($migrationClasses as $migrationClass) {
            $migrations[] = $this->migrationResolver->resolve($migrationClass);
        }

        return $migrations;
    }
}
