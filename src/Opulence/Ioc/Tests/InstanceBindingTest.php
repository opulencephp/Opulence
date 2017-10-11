<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Tests;

use Opulence\Ioc\InstanceBinding;
use stdClass;

/**
 * Tests the instance binding
 */
class InstanceBindingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that the binding is always resolved as a singleton
     */
    public function testAlwaysResolvedAsSingleton()
    {
        $binding = new InstanceBinding(new stdClass());
        $this->assertTrue($binding->resolveAsSingleton());
    }

    /**
     * Tests that the correct instance is returned
     */
    public function testCorrectInstanceIsReturned()
    {
        $instance = new stdClass();
        $binding = new InstanceBinding($instance);
        $this->assertSame($instance, $binding->getInstance());
    }
}
