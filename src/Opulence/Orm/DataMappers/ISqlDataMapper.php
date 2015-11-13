<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Orm\DataMappers;

use Opulence\Orm\Ids\IdGenerator;

/**
 * Defines the interface for SQL data mappers to implement
 */
interface ISqlDataMapper extends IDataMapper
{
    /** Defines a single entity */
    const VALUE_TYPE_ENTITY = 0;
    /** Defines an array of entities */
    const VALUE_TYPE_ARRAY = 1;

    /**
     * Gets the Id generator used by this data mapper
     *
     * @return IdGenerator The Id generator used by this data mapper
     */
    public function getIdGenerator();
} 