<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for repositories to implement
 */
namespace RDev\ORM\Repositories;
use RDev\ORM;
use RDev\ORM\DataMappers;

interface IRepo
{
    /**
     * Adds an entity to the repo
     *
     * @param ORM\IEntity $entity The entity to add
     * @throws ORM\ORMException Thrown if the entity couldn't be added
     */
    public function add(ORM\IEntity &$entity);

    /**
     * Deletes an entity from the repo
     *
     * @param ORM\IEntity $entity The entity to delete
     * @throws ORM\ORMException Thrown if the entity couldn't be deleted
     */
    public function delete(ORM\IEntity &$entity);

    /**
     * Gets all the entities
     *
     * @return ORM\IEntity[] The list of all the entities of this type
     * @throws ORM\ORMException Thrown if there was an error getting the entities
     */
    public function getAll();

    /**
     * Gets the entity with the input Id
     *
     * @param int|string $id The Id of the entity we're searching for
     * @return ORM\IEntity The entity with the input Id
     * @throws ORM\ORMException Thrown if there was no entity with the input Id
     */
    public function getById($id);

    /**
     * Gets the data mapper used by this repository
     *
     * @return DataMappers\IDataMapper The data mapper used by this repository
     */
    public function getDataMapper();

    /**
     * Gets the unit of work used by this repository
     *
     * @return ORM\UnitOfWork The unit of work used by this repository
     */
    public function getUnitOfWork();

    /**
     * Sets the data mapper to use in this repository
     *
     * @param DataMappers\IDataMapper $dataMapper The data mapper to use
     */
    public function setDataMapper($dataMapper);
}