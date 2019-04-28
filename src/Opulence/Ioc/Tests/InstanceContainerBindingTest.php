<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Tests;

use Opulence\Ioc\InstanceContainerBinding;
use stdClass;

/**
 * Tests the instance container binding
 */
class InstanceContainerBindingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that the binding is always resolved as a singleton
     */
    public function testAlwaysResolvedAsSingleton(): void
    {
        $binding = new InstanceContainerBinding(new stdClass());
        $this->assertTrue($binding->resolveAsSingleton());
    }

    /**
     * Tests that the correct instance is returned
     */
    public function testCorrectInstanceIsReturned(): void
    {
        $instance = new stdClass();
        $binding = new InstanceContainerBinding($instance);
        $this->assertSame($instance, $binding->getInstance());
    }
}
