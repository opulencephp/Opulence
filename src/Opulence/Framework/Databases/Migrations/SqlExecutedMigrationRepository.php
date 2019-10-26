<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Databases\Migrations;

use DateTime;
use Opulence\Databases\IConnection;
use Opulence\Databases\Migrations\IExecutedMigrationRepository;
use Opulence\Databases\Providers\Types\Factories\TypeMapperFactory;
use Opulence\QueryBuilders\MySql\QueryBuilder as MySqlQueryBuilder;
use Opulence\QueryBuilders\PostgreSql\QueryBuilder as PostgreSqlQueryBuilder;
use Opulence\QueryBuilders\QueryBuilder as BaseQueryBuilder;
use PDO;
use RuntimeException;

/**
 * Defines the SQL executed migration repository
 */
class SqlExecutedMigrationRepository implements IExecutedMigrationRepository
{
    /** @var string The name of the default table */
    public const DEFAULT_TABLE_NAME = 'executedmigrations';
    /** @var string The name of the table to read and write to */
    protected $tableName = '';
    /** @var IConnection The database connection */
    protected $connection = null;
    /** @var BaseQueryBuilder The query builder */
    protected $queryBuilder = null;
    /** @var TypeMapperFactory The type mapper factory */
    protected $typeMapperFactory = null;

    /**
     * @param string $tableName The name of the table to read and write to
     * @param IConnection $connection The database connection
     * @param BaseQueryBuilder $queryBuilder The query builder
     * @param TypeMapperFactory $typeMapperFactory The type mapper factory
     */
    public function __construct(
        string $tableName,
        IConnection $connection,
        BaseQueryBuilder $queryBuilder,
        TypeMapperFactory $typeMapperFactory
    ) {
        $this->tableName = $tableName;
        $this->connection = $connection;
        $this->queryBuilder = $queryBuilder;
        $this->typeMapperFactory = $typeMapperFactory;
    }

    /**
     * @inheritdoc
     */
    public function add(string $migrationClassName) : void
    {
        $this->createTableIfDoesNotExist();
        $typeMapper = $this->typeMapperFactory->createTypeMapper($this->connection->getDatabaseProvider());
        $query = $this->queryBuilder->insert(
            $this->tableName,
            [
                'migration' => $migrationClassName,
                'dateran' => $typeMapper->toSqlTimestampWithTimeZone(new DateTime())
            ]
        );
        $statement = $this->connection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        $statement->execute();
    }

    /**
     * @inheritdoc
     */
    public function delete(string $migrationClassName) : void
    {
        $this->createTableIfDoesNotExist();
        $query = $this->queryBuilder->delete($this->tableName)
            ->where('migration = :migration')
            ->addNamedPlaceholderValue('migration', $migrationClassName);
        $statement = $this->connection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        $statement->execute();
    }

    /**
     * @inheritdoc
     */
    public function getAll() : array
    {
        $this->createTableIfDoesNotExist();
        $query = $this->queryBuilder->select('migration')
            ->from($this->tableName)
            ->orderBy('dateran DESC');
        $statement = $this->connection->prepare($query->getSql());
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @inheritdoc
     */
    public function getLast(int $number = 1) : array
    {
        $this->createTableIfDoesNotExist();
        $query = $this->queryBuilder->select('migration')
            ->from($this->tableName)
            ->orderBy('dateran DESC')
            ->limit(':number')
            ->addNamedPlaceholderValue('number', $number, PDO::PARAM_INT);
        $statement = $this->connection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Creates the table that the migrations are stored in
     *
     * @throws RuntimeException Thrown if the query builder wasn't a MySQL or PostgreSQL query builder
     */
    protected function createTableIfDoesNotExist() : void
    {
        /**
         * Note: This is a somewhat hacky way to determine which database driver we're using
         * Ideally, in the future, the query builder will be able to create tables for us
         * Until then, we'll infer the database driver from the query builder type
         */
        switch (get_class($this->queryBuilder)) {
            case MySqlQueryBuilder::class:
                $sql = 'CREATE TABLE IF NOT EXISTS ' .
                    $this->tableName .
                    ' (migration varchar(255), dateran timestamp NOT NULL, PRIMARY KEY (migration));';
                break;
            case PostgreSqlQueryBuilder::class:
                $sql = 'CREATE TABLE IF NOT EXISTS ' .
                    $this->tableName .
                    ' (migration text primary key, dateran timestamp with time zone NOT NULL);';
                break;
            default:
                throw new RuntimeException('Unexpected query builder type ' . get_class($this->queryBuilder));
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }
}
