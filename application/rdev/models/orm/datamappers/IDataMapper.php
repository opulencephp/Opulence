<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for data mappers to implement
 */
namespace RDev\Models\ORM\DataMappers;
use RDev\Models;

interface IDataMapper
{
    /**
     * Adds an entity to the database
     *
     * @param Models\IEntity $entity The entity to add
     * @throws Exceptions\DataMapperException Thrown if the entity couldn't be added
     */
    public function add(Models\IEntity &$entity);

    /**
     * Deletes an entity
     *
     * @param Models\IEntity $entity The entity to delete
     * @throws Exceptions\DataMapperException Thrown if the entity couldn't be deleted
     */
    public function delete(Models\IEntity &$entity);

    /**
     * Gets all the entities
     *
     * @return array The list of all the entities
     */
    public function getAll();

    /**
     * Gets the entity with the input Id
     *
     * @param int|string $id The Id of the entity we're searching for
     * @return Models\IEntity The entity with the input Id
     * @throws Exceptions\DataMapperException Thrown if there was no entity with the input Id
     */
    public function getById($id);

    /**
     * Loads an entity from a row of data
     *
     * @param array $hash The hash of data
     * @return Models\IEntity The entity
     */
    public function loadEntity(array $hash);

    /**
     * Saves any changes made to an entity
     *
     * @param Models\IEntity $entity The entity to save
     * @throws Exceptions\DataMapperException Thrown if the entity couldn't be saved
     */
    public function update(Models\IEntity &$entity);
} 