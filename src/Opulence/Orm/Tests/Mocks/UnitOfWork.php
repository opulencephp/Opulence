<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Orm\Tests\Mocks;

use Opulence\Orm\UnitOfWork as BaseUnitOfWork;

/**
 * Mocks the unit of work for testing
 */
class UnitOfWork extends BaseUnitOfWork
{
    /**
     * @inheritDoc
     */
    public function getScheduledEntityDeletions() : array
    {
        $scheduledForDeletion = [];

        foreach ($this->scheduledActions as $action) {
            if (is_array($action) && $action[0] === 'delete') {
                $scheduledForDeletion[] = $action[1];
            }
        }

        return $scheduledForDeletion;
    }

    /**
     * @inheritDoc
     */
    public function getScheduledEntityInsertions() : array
    {
        $scheduledForInsertion = [];

        foreach ($this->scheduledActions as $action) {
            if (is_array($action) && $action[0] === 'insert') {
                $scheduledForInsertion[] = $action[1];
            }
        }

        return $scheduledForInsertion;
    }

    /**
     * @inheritDoc
     */
    public function getScheduledEntityUpdates() : array
    {
        $scheduledForUpdate = [];

        foreach ($this->scheduledActions as $action) {
            if (is_array($action) && $action[0] === 'update') {
                $scheduledForUpdate[] = $action[1];
            }
        }

        return $scheduledForUpdate;
    }
}
