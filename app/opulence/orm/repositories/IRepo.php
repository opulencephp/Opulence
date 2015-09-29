<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for repositories to implement
 */
namespace Opulence\ORM\Repositories;
use Opulence\ORM\IEntity;
use Opulence\ORM\ORMException;

interface IRepo
{
    /**
     * Adds an entity to the repo
     *
     * @param IEntity $entity The entity to add
     * @throws ORMException Thrown if the entity couldn't be added
     */
    public function add(IEntity &$entity);

    /**
     * Deletes an entity from the repo
     *
     * @param IEntity $entity The entity to delete
     * @throws ORMException Thrown if the entity couldn't be deleted
     */
    public function delete(IEntity &$entity);

    /**
     * Gets all the entities
     *
     * @return IEntity[] The list of all the entities of this type
     * @throws ORMException Thrown if there was an error getting the entities
     */
    public function getAll();

    /**
     * Gets the entity with the input Id
     *
     * @param int|string $id The Id of the entity we're searching for
     * @return IEntity The entity with the input Id
     * @throws ORMException Thrown if there was no entity with the input Id
     */
    public function getById($id);
}