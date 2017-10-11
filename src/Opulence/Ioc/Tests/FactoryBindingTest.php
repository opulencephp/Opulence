<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Tests;

use Opulence\Ioc\FactoryBinding;

/**
 * Tests the factory binding
 */
class FactoryBindingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests checking if we should resolve as a singleton
     */
    public function testCheckingIfResolvedAsSingleton()
    {
        $factory = function () {
        };
        $singletonFactory = new FactoryBinding($factory, true);
        $this->assertTrue($singletonFactory->resolveAsSingleton());
        $prototypeFactory = new FactoryBinding($factory, false);
        $this->assertFalse($prototypeFactory->resolveAsSingleton());
    }

    /**
     * Tests getting the factory
     */
    public function testGettingFactory()
    {
        $factory = function () {
        };
        $binding = new FactoryBinding($factory, true);
        $this->assertSame($factory, $binding->getFactory());
    }
}
