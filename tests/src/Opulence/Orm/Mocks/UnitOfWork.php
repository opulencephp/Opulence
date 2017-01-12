<?php
/**
 * Opulence.
 *
 * @link      https://www.opulencephp.com
 *
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Tests\Orm\Mocks;

use Opulence\Orm\UnitOfWork as BaseUnitOfWork;

/**
 * Mocks the unit of work for testing.
 */
class UnitOfWork extends BaseUnitOfWork
{
    /**
     * {@inheritdoc}
     */
    public function getScheduledEntityDeletions() : array
    {
        return parent::getScheduledEntityDeletions();
    }

    /**
     * {@inheritdoc}
     */
    public function getScheduledEntityInsertions() : array
    {
        return parent::getScheduledEntityInsertions();
    }

    /**
     * {@inheritdoc}
     */
    public function getScheduledEntityUpdates() : array
    {
        return parent::getScheduledEntityUpdates();
    }
}
