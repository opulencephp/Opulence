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

use Opulence\Ioc\FactoryContainerBinding;

/**
 * Tests the factory container binding
 */
class FactoryContainerBindingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests checking if we should resolve as a singleton
     */
    public function testCheckingIfResolvedAsSingleton(): void
    {
        $factory = function () {
        };
        $singletonFactory = new FactoryContainerBinding($factory, true);
        $this->assertTrue($singletonFactory->resolveAsSingleton());
        $prototypeFactory = new FactoryContainerBinding($factory, false);
        $this->assertFalse($prototypeFactory->resolveAsSingleton());
    }

    /**
     * Tests getting the factory
     */
    public function testGettingFactory(): void
    {
        $factory = function () {
        };
        $binding = new FactoryContainerBinding($factory, true);
        $this->assertSame($factory, $binding->getFactory());
    }
}
