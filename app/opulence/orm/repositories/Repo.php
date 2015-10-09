<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a generic entity repository that can be extended
 */
namespace Opulence\ORM\Repositories;

use Opulence\ORM\DataMappers\IDataMapper;
use Opulence\ORM\IEntity;
use Opulence\ORM\ORMException;
use Opulence\ORM\UnitOfWork;

class Repo implements IRepo
{
    /** @var string The name of the class whose objects this repo is getting */
    protected $className = "";
    /** @var IDataMapper The data mapper to use in this repo */
    protected $dataMapper = null;
    /** @var UnitOfWork The unit of work to use in this repo */
    protected $unitOfWork = null;

    /**
     * @param string $className The name of the class whose objects this repo is getting
     * @param IDataMapper $dataMapper The data mapper to use in this repo
     * @param UnitOfWork $unitOfWork The unit of work to use in this repo
     */
    public function __construct($className, IDataMapper $dataMapper, UnitOfWork $unitOfWork)
    {
        $this->className = $className;
        $this->unitOfWork = $unitOfWork;
        $this->dataMapper = $dataMapper;
        $this->unitOfWork->registerDataMapper($this->className, $this->dataMapper);
    }

    /**
     * @inheritdoc
     */
    public function add(IEntity &$entity)
    {
        $this->unitOfWork->scheduleForInsertion($entity);
    }

    /**
     * @inheritdoc
     */
    public function delete(IEntity &$entity)
    {
        $this->unitOfWork->scheduleForDeletion($entity);
    }

    /**
     * @inheritdoc
     */
    public function getAll()
    {
        return $this->getFromDataMapper("getAll");
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        $entity = $this->unitOfWork->getEntityRegistry()->getEntity($this->className, $id);

        if ($entity instanceof IEntity) {
            return $entity;
        }

        return $this->getFromDataMapper("getById", [$id]);
    }

    /**
     * Performs a get query on the data mapper and adds any results as managed entities to the unit of work
     *
     * @param string $functionName The name of the function to call in the data mapper
     * @param array $args The list of arguments to pass into the data mapper
     * @return IEntity|IEntity[] The entity or list of entities
     * @throws ORMException Thrown if there was an error getting the entity(ies)
     */
    protected function getFromDataMapper($functionName, array $args = [])
    {
        $entities = call_user_func_array([$this->dataMapper, $functionName], $args);

        if (is_array($entities)) {
            // Passing by reference here is important because that reference may be updated in the unit of work
            foreach ($entities as &$entity) {
                if ($entity instanceof IEntity) {
                    $this->unitOfWork->getEntityRegistry()->register($entity);
                }
            }
        } elseif ($entities instanceof IEntity) {
            $this->unitOfWork->getEntityRegistry()->register($entities);
        }

        return $entities;
    }
} 