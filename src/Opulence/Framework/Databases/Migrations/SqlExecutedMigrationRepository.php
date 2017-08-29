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
use Opulence\QueryBuilders\QueryBuilder;
use PDO;

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
    /** @var QueryBuilder The query builder */
    protected $queryBuilder = null;
    /** @var TypeMapperFactory The type mapper factory */
    protected $typeMapperFactory = null;

    /**
     * @param string $tableName The name of the table to read and write to
     * @param IConnection $connection The database connection
     * @param QueryBuilder $queryBuilder The query builder
     * @param TypeMapperFactory $typeMapperFactory The type mapper factory
     */
    public function __construct(
        string $tableName,
        IConnection $connection,
        QueryBuilder $queryBuilder,
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
        $query = $this->queryBuilder->select('migration')
            ->from($this->tableName)
            ->orderBy('dateran DESC');
        $statement = $this->connection->prepare($query->getSql());
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /**
     * @inheritdoc
     */
    public function getLast(int $number = 1) : array
    {
        $query = $this->queryBuilder->select('migration')
            ->from($this->tableName)
            ->orderBy('dateran DESC')
            ->limit('number')
            ->addNamedPlaceholderValue('number', $number, PDO::PARAM_INT);
        $statement = $this->connection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN, 0);
    }
}
