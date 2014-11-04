<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an Id generator, which is used to create Ids for entities
 */
namespace RDev\ORM\Ids;
use RDev\Databases\SQL;
use RDev\ORM;

abstract class IdGenerator
{
    /**
     * Generates an Id for an entity
     *
     * @param ORM\IEntity $entity The entity whose Id we're generating
     * @param SQL\IConnection $connection The connection to use to get the Id
     * @return mixed The Id of the entity
     */
    abstract public function generate(ORM\IEntity $entity, SQL\IConnection $connection);

    /**
     * Gets the value of the Id when it isn't set
     *
     * @return mixed The Id of an entity when it isn't set
     */
    abstract public function getEmptyValue();
} 