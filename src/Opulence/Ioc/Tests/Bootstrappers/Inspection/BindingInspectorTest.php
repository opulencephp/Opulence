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
use Opulence\Ioc\Bootstrappers\Inspection\BindingInspectionContainer;
use Opulence\Ioc\Bootstrappers\Inspection\BindingInspector;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\Tests\Bootstrappers\Inspection\Mocks\Foo;
use Opulence\Ioc\Tests\Bootstrappers\Inspection\Mocks\IFoo;
use PHPUnit\Framework\TestCase;

/**
 * Tests the binding inspector
 */
class BindingInspectorTest extends TestCase
{
    /** @var BindingInspector */
    private $inspector;
    /** @var BindingInspectionContainer */
    private $container;

    protected function setUp(): void
    {
        $this->container = new BindingInspectionContainer();
        $this->inspector = new BindingInspector($this->container);
    }

    public function testInspectingBindingsCreatesBindingsFromWhatIsBoundInBootstrapper(): void
    {
        $expectedBootstrapper = new class extends Bootstrapper {
            public function registerBindings(IContainer $container): void
            {
                $container->bindInstance(IFoo::class, new Foo());
            }
        };
        $actualBindings = $this->inspector->getBindings([$expectedBootstrapper]);
        $this->assertCount(1, $actualBindings);
        $this->assertEquals(IFoo::class, $actualBindings[0]->getInterface());
        $this->assertSame($expectedBootstrapper, $actualBindings[0]->getBootstrapper());
    }
}
