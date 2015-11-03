<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
/**
 * Defines an Id generator, which is used to create Ids for entities
 */
namespace Opulence\Orm\Ids;

use Opulence\Databases\IConnection;

abstract class IdGenerator
{
    /**
     * Generates an Id for an entity
     *
     * @param object $entity The entity whose Id we're generating
     * @param IConnection $connection The connection to use to get the Id
     * @return mixed The Id of the entity
     */
    abstract public function generate($entity, IConnection $connection);

    /**
     * Gets the value of the Id when it isn't set
     *
     * @return mixed The Id of an entity when it isn't set
     */
    abstract public function getEmptyValue();
}