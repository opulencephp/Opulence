<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for repositories to implement
 */
namespace RDev\ORM\Repositories;
use RDev\ORM\DataMappers\IDataMapper;
use RDev\ORM\IEntity;
use RDev\ORM\ORMException;
use RDev\ORM\UnitOfWork;

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

    /**
     * Gets the data mapper used by this repository
     *
     * @return IDataMapper The data mapper used by this repository
     */
    public function getDataMapper();

    /**
     * Gets the unit of work used by this repository
     *
     * @return UnitOfWork The unit of work used by this repository
     */
    public function getUnitOfWork();

    /**
     * Sets the data mapper to use in this repository
     *
     * @param IDataMapper $dataMapper The data mapper to use
     */
    public function setDataMapper(IDataMapper $dataMapper);
}