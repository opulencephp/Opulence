<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\Repositories;

use Opulence\Orm\DataMappers\IDataMapper;
use Opulence\Orm\IUnitOfWork;
use Opulence\Orm\OrmException;

/**
 * Defines a generic entity repository that can be extended
 */
class Repository implements IRepository
{
    /** @var string The name of the class whose objects this repo is getting */
    protected string $className = '';
    /** @var IDataMapper The data mapper to use in this repo */
    protected IDataMapper $dataMapper;
    /** @var IUnitOfWork The unit of work to use in this repo */
    protected IUnitOfWork $unitOfWork;

    /**
     * @param string $className The name of the class whose objects this repo is getting
     * @param IDataMapper $dataMapper The data mapper to use in this repo
     * @param IUnitOfWork $unitOfWork The unit of work to use in this repo
     */
    public function __construct(string $className, IDataMapper $dataMapper, IUnitOfWork $unitOfWork)
    {
        $this->className = $className;
        $this->unitOfWork = $unitOfWork;
        $this->dataMapper = $dataMapper;
        $this->unitOfWork->registerDataMapper($this->className, $this->dataMapper);
    }

    /**
     * @inheritdoc
     */
    public function add(object $entity): void
    {
        $this->unitOfWork->scheduleForInsertion($entity);
    }

    /**
     * @inheritdoc
     */
    public function delete(object $entity): void
    {
        $this->unitOfWork->scheduleForDeletion($entity);
    }

    /**
     * @inheritdoc
     */
    public function getById($id): object
    {
        $entity = $this->unitOfWork->getEntityRegistry()->getEntity($this->className, $id);

        if ($entity !== null) {
            return $entity;
        }

        $entity = $this->getFromDataMapper('getById', [$id]);

        if ($entity === null) {
            throw new OrmException("No entity exists with {$id}");
        }

        return $entity;
    }

    /**
     * Performs a get query on the data mapper and adds any results as managed entities to the unit of work
     *
     * @param string $functionName The name of the function to call in the data mapper
     * @param array $args The list of arguments to pass into the data mapper
     * @return object|object[] The entity or list of entities
     * @throws OrmException Thrown if there was an error getting the entity(ies)
     */
    protected function getFromDataMapper(string $functionName, array $args = [])
    {
        $entities = $this->dataMapper->$functionName(...$args);

        if (is_array($entities)) {
            // Passing by reference here is important because that reference may be updated in the unit of work
            foreach ($entities as &$entity) {
                if ($entity !== null) {
                    $this->unitOfWork->getEntityRegistry()->registerEntity($entity);
                }
            }
        } elseif ($entities !== null) {
            $this->unitOfWork->getEntityRegistry()->registerEntity($entities);
        }

        return $entities;
    }
}
