<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Orm;

/**
 * Defines the interface for entities to optionally implement
 */
interface IEntity
{
    /**
     * Gets the unique identifier
     *
     * @return int|string The Id
     */
    public function getId();

    /**
     * Sets the unique identifier of the entity
     *
     * @param int|string $id The Id
     */
    public function setId($id);
}
