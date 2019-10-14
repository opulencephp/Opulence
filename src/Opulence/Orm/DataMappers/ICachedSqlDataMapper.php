<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\DataMappers;

use Opulence\Orm\OrmException;

/**
 * Defines the interface for data mappers whose data is cached
 */
interface ICachedSqlDataMapper extends IDataMapper
{
    /**
     * Performs any cache actions that have been scheduled
     * This is best used when committing an SQL data mapper via a unit of work, and then calling this method after
     * the commit successfully finishes
     *
     * @throws OrmException Thrown if there was an error committing to cache
     */
    public function commit(): void;

    /**
     * Gets the cache data mapper
     *
     * @return ICacheDataMapper The cache data mapper
     */
    public function getCacheDataMapper(): ICacheDataMapper;

    /**
     * Gets the SQL data mapper
     *
     * @return SqlDataMapper The SQL data mapper
     */
    public function getSqlDataMapper(): SqlDataMapper;

    /**
     * Refreshes an entity in cache with the entity from the SQL data mapper
     *
     * @param int|string $id The Id of the entity to sync
     * @throws OrmException Thrown if there was an error refreshing the entity
     */
    public function refreshEntity($id): void;
}
