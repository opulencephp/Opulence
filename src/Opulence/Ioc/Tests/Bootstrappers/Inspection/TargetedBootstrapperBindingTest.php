<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Tests\Bootstrappers\Inspection;

use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\Inspection\TargetedBootstrapperBinding;
use Opulence\Ioc\IContainer;
use PHPUnit\Framework\TestCase;

/**
 * Tests the targeted bootstrapper binding
 */
class TargetedBootstrapperBindingTest extends TestCase
{
    public function testGettingPropertiesReturnsOneSetInConstructor(): void
    {
        $expectedBootstrapper = new class extends Bootstrapper {
            public function registerBindings(IContainer $container): void
            {
                // Don't do anything
            }
        };
        $binding = new TargetedBootstrapperBinding('foo', 'bar', $expectedBootstrapper);
        $this->assertEquals('foo', $binding->getTargetClass());
        $this->assertEquals('bar', $binding->getInterface());
        $this->assertSame($expectedBootstrapper, $binding->getBootstrapper());
    }
}
