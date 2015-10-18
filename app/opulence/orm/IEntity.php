<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for entities to optionally implement
 */
namespace Opulence\ORM;

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