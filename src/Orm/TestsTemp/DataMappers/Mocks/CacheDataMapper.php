<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\TestsTemp\DataMappers\Mocks;

use Opulence\Orm\DataMappers\ICacheDataMapper;

/**
 * Mocks the cache data mapper class for use in testing
 */
class CacheDataMapper implements ICacheDataMapper
{
    /** @var object[] The list of entities added */
    protected array $entities = [];

    public function __construct()
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    public function add($entity): void
    {
        $this->entities[$entity->getId()] = $entity;
    }

    /**
     * @inheritdoc
     */
    public function delete($entity): void
    {
        unset($this->entities[$entity->getId()]);
    }

    /**
     * @inheritdoc
     */
    public function flush(): void
    {
        $this->entities = [];
    }

    /**
     * @inheritdoc
     */
    public function getAll(): array
    {
        return array_values($this->entities);
    }

    /**
     * @inheritdoc
     */
    public function getById($id): ?object
    {
        if (!isset($this->entities[$id])) {
            return null;
        }

        return $this->entities[$id];
    }

    /**
     * @inheritdoc
     */
    public function update($entity): void
    {
        $this->entities[$entity->getId()] = $entity;
    }
}
