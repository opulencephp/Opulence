<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Framework\Tests\Databases\Migrations;

use Opulence\Databases\Migrations\IMigration;
use Opulence\Databases\Migrations\MigrationResolutionException;
use Opulence\Framework\Databases\Migrations\ContainerMigrationResolver;
use Opulence\Ioc\IocException;

/**
 * Tests the container migration resolver
 */
class ContainerMigrationResolverTest
{
    /** @var ContainerMigrationResolver The migration resolver to use in tests */
    private $migrationResolver = null;
    /** @var IContainer|\PHPUnit_Framework_MockObject_MockObject The IoC container to use in tests */
    private $container = null;

    /**
     * Sets up tests
     */
    public function setUp() : void
    {
        $this->container = $this->createMock(IContainer::class);
        $this->migrationResolver = new ContainerMigrationResolver($this->container);
    }

    /**
     * Tests that the container is used to resolve migrations
     */
    public function testContainerIsUsedToResolveDependencies() : void
    {
        $migration = $this->createMock(IMigration::class);
        $this->container->expects($this->once())
            ->method('resolve')
            ->with('foo')
            ->willReturn($migration);
        $this->assertEquals($migration, $this->migrationResolver->resolve('foo'));
    }

    /**
     * Tests that IoC exceptions are converted
     */
    public function testIocExceptionsAreConverted() : void
    {
        $this->expectException(MigrationResolutionException::class);
        $this->container->expects($this->once())
            ->method('resolve')
            ->with('foo')
            ->willThrowException(new IocException('blah'));
        $this->migrationResolver->resolve('foo');
    }
}
