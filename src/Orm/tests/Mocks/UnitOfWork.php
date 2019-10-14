<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\Tests\Mocks;

use Opulence\Orm\UnitOfWork as BaseUnitOfWork;

/**
 * Mocks the unit of work for testing
 */
class UnitOfWork extends BaseUnitOfWork
{
    /**
     * @inheritdoc
     */
    public function getScheduledEntityDeletions(): array
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
     * @inheritdoc
     */
    public function getScheduledEntityInsertions(): array
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
     * @inheritdoc
     */
    public function getScheduledEntityUpdates(): array
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
