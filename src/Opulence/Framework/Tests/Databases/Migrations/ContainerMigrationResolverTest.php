<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Tests\Databases\Migrations;

use Opulence\Databases\Migrations\IMigration;
use Opulence\Databases\Migrations\MigrationResolutionException;
use Opulence\Framework\Databases\Migrations\ContainerMigrationResolver;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the container migration resolver
 */
class ContainerMigrationResolverTest extends \PHPUnit\Framework\TestCase
{
    private ContainerMigrationResolver $migrationResolver;
    /** @var IContainer|MockObject The IoC container to use in tests */
    private IContainer $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(IContainer::class);
        $this->migrationResolver = new ContainerMigrationResolver($this->container);
    }

    public function testContainerIsUsedToResolveDependencies(): void
    {
        $migration = $this->createMock(IMigration::class);
        $this->container->expects($this->once())
            ->method('resolve')
            ->with('foo')
            ->willReturn($migration);
        $this->assertEquals($migration, $this->migrationResolver->resolve('foo'));
    }

    public function testIocExceptionsAreConverted(): void
    {
        $this->expectException(MigrationResolutionException::class);
        $this->container->expects($this->once())
            ->method('resolve')
            ->with('foo')
            ->willThrowException(new IocException('blah'));
        $this->migrationResolver->resolve('foo');
    }
}
