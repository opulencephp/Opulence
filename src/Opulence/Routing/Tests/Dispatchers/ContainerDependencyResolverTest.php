<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Routing\Tests\Dispatchers;

use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;
use Opulence\Routing\Dispatchers\ContainerDependencyResolver;
use Opulence\Routing\Dispatchers\DependencyResolutionException;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the dependency resolver
 */
class ContainerDependencyResolverTest extends \PHPUnit\Framework\TestCase
{
    /** @var ContainerDependencyResolver The dependency resolver to use in tests */
    private $dependencyResolver;
    /** @var IContainer|MockObject The IoC container to use in tests */
    private $container;

    /**
     * Sets up tests
     */
    protected function setUp(): void
    {
        $this->container = $this->createMock(IContainer::class);
        $this->dependencyResolver = new ContainerDependencyResolver($this->container);
    }

    /**
     * Tests that the container is used to resolve dependencies
     */
    public function testContainerIsUsedToResolveDependencies(): void
    {
        $this->container->expects($this->once())
            ->method('resolve')
            ->with('foo')
            ->willReturn($this);
        $this->assertEquals($this, $this->dependencyResolver->resolve('foo'));
    }

    /**
     * Tests that IoC exceptions are converted
     */
    public function testIocExceptionsAreConverted(): void
    {
        $this->expectException(DependencyResolutionException::class);
        $this->container->expects($this->once())
            ->method('resolve')
            ->with('foo')
            ->willThrowException(new IocException('blah'));
        $this->dependencyResolver->resolve('foo');
    }
}
