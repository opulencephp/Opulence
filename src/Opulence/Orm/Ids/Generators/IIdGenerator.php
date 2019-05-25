<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\Ids\Generators;

/**
 * Defines the interface for Id generators to implement
 */
interface IIdGenerator
{
    /**
     * Generates an Id for an entity
     *
     * @param object $entity The entity whose Id we're generating
     * @return mixed The Id of the entity
     */
    public function generate(object $entity);

    /**
     * Gets the value of the Id when it isn't set
     *
     * @param object $entity The entity whose empty Id value we want
     * @return mixed The Id of an entity when it isn't set
     */
    public function getEmptyValue(object $entity);

    /**
     * Gets whether or not the generator should be executed post-insert
     *
     * @return bool True if the generator should be executed post-insert, otherwise false
     */
    public function isPostInsert(): bool;
}
