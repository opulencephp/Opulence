<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Tests\Dispatchers;

use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;
use Opulence\Routing\Dispatchers\ContainerDependencyResolver;
use Opulence\Routing\Dispatchers\DependencyResolutionException;

/**
 * Tests the dependency resolver
 */
class ContainerDependencyResolverTest extends \PHPUnit\Framework\TestCase
{
    /** @var ContainerDependencyResolver The dependency resolver to use in tests */
    private $dependencyResolver = null;
    /** @var IContainer|\PHPUnit_Framework_MockObject_MockObject The IoC container to use in tests */
    private $container = null;

    /**
     * Sets up tests
     */
    public function setUp() : void
    {
        $this->container = $this->createMock(IContainer::class);
        $this->dependencyResolver = new ContainerDependencyResolver($this->container);
    }

    /**
     * Tests that the container is used to resolve dependencies
     */
    public function testContainerIsUsedToResolveDependencies() : void
    {
        $this->container->expects($this->once())
            ->method('resolve')
            ->with('foo')
            ->willReturn([]);
        $this->assertEquals([], $this->dependencyResolver->resolve('foo'));
    }

    /**
     * Tests that IoC exceptions are converted
     */
    public function testIocExceptionsAreConverted() : void
    {
        $this->expectException(DependencyResolutionException::class);
        $this->container->expects($this->once())
            ->method('resolve')
            ->with('foo')
            ->willThrowException(new IocException('blah'));
        $this->dependencyResolver->resolve('foo');
    }
}
