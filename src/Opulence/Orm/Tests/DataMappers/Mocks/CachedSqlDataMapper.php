<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\Tests\DataMappers\Mocks;

use Opulence\Databases\IConnection;
use Opulence\Orm\DataMappers\CachedSqlDataMapper as BaseCachedSqlDataMapper;
use Opulence\Orm\DataMappers\ICacheDataMapper;
use Opulence\Orm\Ids\Accessors\IIdAccessorRegistry;

/**
 * Mocks the cached SQL data mapper for use in tests
 */
class CachedSqlDataMapper extends BaseCachedSqlDataMapper
{
    /**
     * @param SqlDataMapper $sqlDataMapper The SQL data mapper to use
     * @param ICacheDataMapper $cacheDataMapper The cache data mapper to use
     * @param IIdAccessorRegistry $idAccessorRegistry The Id accessor registry to use
     */
    public function __construct(
        SqlDataMapper $sqlDataMapper = null,
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
    public function getAll(): array
    {
        return $this->read('getAll');
    }

    /**
     * @inheritdoc
     */
    public function getById($id): ?object
    {
        return $this->read('getById', [$id]);
    }

    /**
     * @inheritdoc
     */
    protected function setCacheDataMapper($cache): void
    {
        $this->cacheDataMapper = new CacheDataMapper();
    }

    /**
     * @inheritdoc
     */
    protected function setSqlDataMapper(IConnection $readConnection, IConnection $writeConnection): void
    {
        $this->sqlDataMapper = new SqlDataMapper();
    }
}
