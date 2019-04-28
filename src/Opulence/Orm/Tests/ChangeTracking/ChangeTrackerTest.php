<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\Tests\ChangeTracking;

use Opulence\Orm\ChangeTracking\ChangeTracker;
use Opulence\Orm\OrmException;
use Opulence\Orm\Tests\ChangeTracking\Mocks\User;

/**
 * Tests the change tracker
 */
class ChangeTrackerTest extends \PHPUnit\Framework\TestCase
{
    /** @var ChangeTracker The change tracker to use in tests */
    private $changeTracker;
    /** @var User An entity to use in the tests */
    private $entity1;
    /** @var User An entity to use in the tests */
    private $entity2;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->changeTracker = new ChangeTracker();
        /**
         * The Ids are purposely unique so that we can identify them as such without having to first insert them to
         * assign unique Ids
         * They are also purposely set to 724 and 1987 so that they won't potentially overlap with any default values
         * set to the Ids
         */
        $this->entity1 = new User(724, 'foo');
        $this->entity2 = new User(1987, 'bar');
    }

    /**
     * Tests seeing if a change is detected with a comparison function
     */
    public function testCheckingForChangeWithComparisonFunction(): void
    {
        $className = get_class($this->entity1);
        $this->changeTracker->startTracking($this->entity1);
        $this->changeTracker->startTracking($this->entity2);
        $this->entity1->setUsername("not entity 1's username");
        $this->changeTracker->registerComparator($className, function ($a, $b) {
            /** @var User $a */
            /** @var User $b */
            return $a->getId() == $b->getId();
        });
        $this->assertFalse($this->changeTracker->hasChanged($this->entity1));
    }

    /**
     * Tests seeing if a change is detected without a comparison function
     */
    public function testCheckingForChangeWithoutComparisonFunction(): void
    {
        $this->changeTracker->startTracking($this->entity1);
        $this->entity1->setUsername('blah');
        $this->assertTrue($this->changeTracker->hasChanged($this->entity1));
    }

    /**
     * Tests checking for changes on an unregistered entity
     */
    public function testCheckingForChangesOnUnregisteredEntity(): void
    {
        $this->expectException(OrmException::class);
        $this->changeTracker->hasChanged($this->entity1);
    }

    /**
     * Tests checking that nothing has changed with a comparison function
     */
    public function testCheckingForNoChangeWithComparisonFunction(): void
    {
        $className = get_class($this->entity1);
        $this->changeTracker->startTracking($this->entity1);
        $this->changeTracker->registerComparator($className, function ($a, $b) {
            return false;
        });
        $this->assertTrue($this->changeTracker->hasChanged($this->entity1));
    }

    /**
     * Tests checking that nothing has changed without a comparison function
     */
    public function testCheckingForNoChangeWithoutComparisonFunction(): void
    {
        $this->changeTracker->startTracking($this->entity1);
        $this->assertFalse($this->changeTracker->hasChanged($this->entity1));
    }
}
