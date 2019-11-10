<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Databases\Console\Commands;

use Exception;
use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;
use Opulence\Databases\IConnection;
use Opulence\QueryBuilders\MySql\QueryBuilder as MySqlQueryBuilder;
use Opulence\QueryBuilders\PostgreSql\QueryBuilder as PostgreSqlQueryBuilder;
use Opulence\QueryBuilders\QueryBuilder as BaseQueryBuilder;
use RuntimeException;

/**
 * Defines the command that fixes migrations
 */
class FixMigrationsCommand extends Command
{
    /** @var string[] The list of migration classes */
    private $allMigrationClasses = [];

    /** @var string The name of the table to read and write to */
    protected $tableName = '';

    /** @var IConnection */
    protected $connection;

    /** @var BaseQueryBuilder */
    protected $queryBuilder;

    /**
     * FixMigrationsCommand constructor.
     *
     * @param string           $tableName           The name of the table to read and write to
     * @param string[]         $allMigrationClasses The list of migration classes
     * @param IConnection      $connection
     * @param BaseQueryBuilder $queryBuilder
     */
    public function __construct(
        string $tableName,
        array $allMigrationClasses,
        IConnection $connection,
        BaseQueryBuilder $queryBuilder
    ) {
        parent::__construct();

        $this->tableName           = $tableName;
        $this->allMigrationClasses = $allMigrationClasses;
        $this->connection          = $connection;
        $this->queryBuilder        = $queryBuilder;
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName('migrations:fix')
            ->setDescription('Fixes the migrations database schema');
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $response->writeln('Fixing migrations database schema...');

        try {
            $this->connection->beginTransaction();

            switch (get_class($this->queryBuilder)) {
                case MySqlQueryBuilder::class:
                    $this->addPrimaryKeyMySql();
                    break;
                case PostgreSqlQueryBuilder::class:
                    $this->addPrimaryKeyPostgreSql();
                    break;
                default:
                    throw new RuntimeException('Unexpected query builder type ' . get_class($this->queryBuilder));
            }

            $this->connection->commit();
        } catch (Exception $e) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            $response->writeln(sprintf('<fatal>%s</fatal>', $e->getMessage()));
        }
    }

    /**
     *
     */
    protected function addPrimaryKeyMySql(): void
    {
        $sql       = sprintf('ALTER TABLE %s ADD COLUMN id int not null FIRST', $this->tableName);
        $statement = $this->connection->prepare($sql);
        if (!$statement->execute()) {
            throw new RuntimeException(sprintf('statement error: %s', json_encode($statement->errorInfo())));
        }

        $this->updatePrimaryKey();

        $sql       = sprintf(
            'ALTER TABLE %s DROP primary key, ADD primary key (id), MODIFY id int not null auto_increment',
            $this->tableName
        );
        $statement = $this->connection->prepare($sql);
        if (!$statement->execute()) {
            throw new RuntimeException(sprintf('statement error: %s', json_encode($statement->errorInfo())));
        }
    }

    /**
     *
     */
    protected function addPrimaryKeyPostgreSql(): void
    {
        $sql       = sprintf('ALTER TABLE %s ADD COLUMN id int not null', $this->tableName);
        $statement = $this->connection->prepare($sql);
        if (!$statement->execute()) {
            throw new RuntimeException(sprintf('statement error: %s', json_encode($statement->errorInfo())));
        }

        $this->updatePrimaryKey();

        $sql       = sprintf('ALTER TABLE %s DROP CONSTRAINT %s_pkey', $this->tableName, $this->tableName);
        $statement = $this->connection->prepare($sql);
        if (!$statement->execute()) {
            throw new RuntimeException(sprintf('statement error: %s', json_encode($statement->errorInfo())));
        }

        $sql       = sprintf('ALTER TABLE %s MODIFY id serial primary key', $this->tableName);
        $statement = $this->connection->prepare($sql);
        if (!$statement->execute()) {
            throw new RuntimeException(sprintf('statement error: %s', json_encode($statement->errorInfo())));
        }

        $sql       = sprintf('ALTER TABLE %s ADD PRIMARY KEY (id)', $this->tableName);
        $statement = $this->connection->prepare($sql);
        if (!$statement->execute()) {
            throw new RuntimeException(sprintf('statement error: %s', json_encode($statement->errorInfo())));
        }
    }


    /**
     * Update each migration entries with a proper primary key
     */
    protected function updatePrimaryKey(): void
    {
        foreach ($this->allMigrationClasses as $i => $migrationClass) {
            $query = $this->queryBuilder
                ->update($this->tableName, $this->tableName, ['id' => $i + 1])
                ->where('migration = ?')
                ->addUnnamedPlaceholderValue($migrationClass);

            $statement = $this->connection->prepare($query->getSql());
            $statement->bindValues($query->getParameters());
            if (!$statement->execute()) {
                throw new RuntimeException(sprintf('statement error: %s', json_encode($statement->errorInfo())));
            }

            $n = $statement->rowCount();
        }
    }
}
