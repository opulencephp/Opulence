<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a generic entity repository that can be extended
 */
namespace RDev\Models\ORM\Repositories;
use RDev\Models;
use RDev\Models\ORM;
use RDev\Models\ORM\DataMappers;

class Repo
{
    /** @var string The name of the class whose objects this repo is getting */
    protected $className = "";
    /** @var DataMappers\IDataMapper The data mapper to use in this repo */
    protected $dataMapper = null;
    /** @var ORM\UnitOfWork The unit of work to use in this repo */
    protected $unitOfWork = null;

    /**
     * @param string $className The name of the class whose objects this repo is getting
     * @param DataMappers\IDataMapper $dataMapper The data mapper to use in this repo
     * @param ORM\UnitOfWork $unitOfWork The unit of work to use in this repo
     */
    public function __construct($className, DataMappers\IDataMapper $dataMapper, ORM\UnitOfWork $unitOfWork)
    {
        $this->className = $className;
        $this->dataMapper = $dataMapper;
        $this->unitOfWork = $unitOfWork;
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
    }

    /**
     * Adds an entity to the repo
     *
     * @param Models\IEntity $entity The entity to add
     * @throws DataMappers\Exceptions\DataMapperException Thrown if the entity couldn't be added
     */
    public function add(Models\IEntity &$entity)
    {
        $this->unitOfWork->scheduleForInsertion($entity);
    }

    /**
     * Deletes an entity from the repo
     *
     * @param Models\IEntity $entity The entity to delete
     * @throws DataMappers\Exceptions\DataMapperException Thrown if the entity couldn't be deleted
     */
    public function delete(Models\IEntity &$entity)
    {
        $this->unitOfWork->scheduleForDeletion($entity);
    }

    /**
     * Gets all the entities
     *
     * @return array The list of all the entities of this type
     * @throws DataMappers\Exceptions\DataMapperException Thrown if there was an error getting the entities
     */
    public function getAll()
    {
        return $this->get("getAll");
    }

    /**
     * Gets the entity with the input Id
     *
     * @param int $id The Id of the entity we're searching for
     * @return Models\IEntity The entity with the input Id
     * @throws DataMappers\Exceptions\DataMapperException Thrown if there was no entity with the input Id
     */
    public function getById($id)
    {
        $entity = $this->unitOfWork->getManagedEntity($this->className, $id);

        if($entity instanceof Models\IEntity)
        {
            return $entity;
        }

        return $this->get("getById", [$id]);
    }

    /**
     * Performs a get query on the data mapper and adds any results as managed entities to the unit of work
     *
     * @param string $functionName The name of the function to call in the data mapper
     * @param array $args The list of arguments to pass into the data mapper
     * @return Models\IEntity|array The entity or list of entities
     * @throws DataMappers\Exceptions\DataMapperException Thrown if there was an error getting the entity(ies)
     */
    protected function get($functionName, $args = [])
    {
        $entities = call_user_func_array([$this->dataMapper, $functionName], $args);
        $this->unitOfWork->manageEntities($entities);

        return $entities;
    }
} 