<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for repositories to implement
 */
namespace RDev\Models\ORM\Repositories;
use RDev\Models;
use RDev\Models\ORM;
use RDev\Models\ORM\DataMappers;

interface IRepo
{
    /**
     * Adds an entity to the repo
     *
     * @param Models\IEntity $entity The entity to add
     * @throws ORM\ORMException Thrown if the entity couldn't be added
     */
    public function add(Models\IEntity &$entity);

    /**
     * Deletes an entity from the repo
     *
     * @param Models\IEntity $entity The entity to delete
     * @throws ORM\ORMException Thrown if the entity couldn't be deleted
     */
    public function delete(Models\IEntity &$entity);

    /**
     * Gets all the entities
     *
     * @return array The list of all the entities of this type
     * @throws ORM\ORMException Thrown if there was an error getting the entities
     */
    public function getAll();

    /**
     * Gets the entity with the input Id
     *
     * @param int|string $id The Id of the entity we're searching for
     * @return Models\IEntity The entity with the input Id
     * @throws ORM\ORMException Thrown if there was no entity with the input Id
     */
    public function getById($id);

    /**
     * @return DataMappers\IDataMapper
     */
    public function getDataMapper();

    /**
     * @param DataMappers\IDataMapper $dataMapper
     */
    public function setDataMapper($dataMapper);
}