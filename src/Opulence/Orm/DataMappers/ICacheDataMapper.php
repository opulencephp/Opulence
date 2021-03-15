<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Orm\DataMappers;

use Opulence\Orm\OrmException;

/**
 * Defines the interface for cache data mappers to implement
 */
interface ICacheDataMapper extends IDataMapper
{
    /**
     * Flushes entities stored by this data mapper from cache
     *
     * @throws OrmException Thrown if the cache couldn't be flushed
     */
    public function flush();
}
