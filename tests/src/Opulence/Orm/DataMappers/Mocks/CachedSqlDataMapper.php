<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Orm\DataMappers\Mocks;

use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\Orm\Ids\IIdAccessorRegistry;
use Opulence\Orm\DataMappers\CachedSqlDataMapper as BaseCachedSqlDataMapper;
use Opulence\Orm\DataMappers\ICacheDataMapper;
use Opulence\Orm\DataMappers\ISqlDataMapper;

/**
 * Mocks the cached SQL data mapper for use in tests
 */
class CachedSqlDataMapper extends BaseCachedSqlDataMapper
{
    /**
     * @param ISQLDataMapper $sqlDataMapper The SQL data mapper to use
     * @param ICacheDataMapper $cacheDataMapper The cache data mapper to use
     * @param IIdAccessorRegistry $idAccessorRegistry The Id accessor registry to use
     */
    public function __construct(
        ISqlDataMapper $sqlDataMapper = null,
        ICacheDataMapper $cacheDataMapper = null,
        IIdAccessorRegistry $idAccessorRegistry = null
    ) {
        if ($sqlDataMapper === null) {
            $sqlDataMapper = new SqlDataMapper();
        }

        if ($cacheDataMapper === null) {
            $cacheDataMapper = new CacheDataMapper();
        }

        $this->sqlDataMapper = $sqlDataMapper;
        $this->cacheDataMapper = $cacheDataMapper;
        $this->idAccessorRegistry = $idAccessorRegistry;
    }

    /**
     * @inheritdoc
     */
    public function getAll()
    {
        return $this->read("getAll");
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        return $this->read("getById", [$id]);
    }

    /**
     * @inheritdoc
     */
    public function loadEntity(array $hash)
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    protected function setCacheDataMapper($cache)
    {
        $this->cacheDataMapper = new CacheDataMapper();
    }

    /**
     * @inheritdoc
     */
    protected function setSqlDataMapper(ConnectionPool $connectionPool)
    {
        $this->sqlDataMapper = new SqlDataMapper();
    }
} 