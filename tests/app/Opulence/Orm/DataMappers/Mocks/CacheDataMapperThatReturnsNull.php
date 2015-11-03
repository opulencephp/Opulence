<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Orm\DataMappers\Mocks;

/**
 * Mocks a cache data mapper that returns null
 */
class CacheDataMapperThatReturnsNull extends CacheDataMapper
{
    /**
     * @inheritdoc
     */
    public function getAll()
    {
        return null;
    }
}