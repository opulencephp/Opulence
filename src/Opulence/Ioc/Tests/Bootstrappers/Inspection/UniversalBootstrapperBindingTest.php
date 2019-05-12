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
use Opulence\Ioc\Bootstrappers\Inspection\UniversalBootstrapperBinding;
use Opulence\Ioc\IContainer;
use PHPUnit\Framework\TestCase;

/**
 * Tests the universal bootstrapper binding
 */
class UniversalBootstrapperBindingTest extends TestCase
{
    public function testGettingPropertiesReturnsOneSetInConstructor(): void
    {
        $expectedBootstrapper = new class extends Bootstrapper {
            public function registerBindings(IContainer $container): void
            {
                // Don't do anything
            }
        };
        $binding = new UniversalBootstrapperBinding('foo', $expectedBootstrapper);
        $this->assertEquals('foo', $binding->getInterface());
        $this->assertSame($expectedBootstrapper, $binding->getBootstrapper());
    }
}
