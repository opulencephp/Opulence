<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Databases\Tests\Migrations;

use Opulence\Databases\IConnection;
use Opulence\Databases\Migrations\IExecutedMigrationRepository;
use Opulence\Databases\Migrations\IMigration;
use Opulence\Databases\Migrations\IMigrationResolver;
use Opulence\Databases\Migrations\Migrator;

/**
 * Tests the database migrator
 */
class MigratorTest extends \PHPUnit\Framework\TestCase
{
    /** @var IConnection|\PHPUnit_Framework_MockObject_MockObject The database connection the migrator uses */
    private $connection = null;
    /** @var IMigrationResolver|\PHPUnit_Framework_MockObject_MockObject The migration resolver */
    private $migrationResolver = null;
    /** @var IExecutedMigrationRepository|\PHPUnit_Framework_MockObject_MockObject The executed migration repository */
    private $executedMigrations = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->connection = $this->createMock(IConnection::class);
        $this->migrationResolver = $this->createMock(IMigrationResolver::class);
        $this->executedMigrations = $this->createMock(IExecutedMigrationRepository::class);
    }

    /**
     * Tests that rolling back all migrations calls down on all migrations
     */
    public function testRollingBackAllMigrationsCallsDownOnAllMigrations() : void
    {
        $migrator = new Migrator(
            ['foo', 'bar'],
            $this->connection,
            $this->migrationResolver,
            $this->executedMigrations
        );
        $this->executedMigrations->expects($this->once())
            ->method('getAll')
            ->willReturn(['foo', 'bar']);
        $expectedMigration1 = $this->createMock(IMigration::class);
        $expectedMigration1->expects($this->once())
            ->method('down');
        $expectedMigration2 = $this->createMock(IMigration::class);
        $expectedMigration2->expects($this->once())
            ->method('down');
        $this->migrationResolver->expects($this->at(0))
            ->method('resolve')
            ->with('foo')
            ->willReturn($expectedMigration1);
        $this->migrationResolver->expects($this->at(1))
            ->method('resolve')
            ->with('bar')
            ->willReturn($expectedMigration2);
        $this->connection->expects($this->once())
            ->method('beginTransaction');
        $this->connection->expects($this->once())
            ->method('commit');
        $this->assertEquals(['foo', 'bar'], $migrator->rollBackAllMigrations());
    }

    /**
     * Tests that rolling back a specific number of migrations calls down on those migrations
     */
    public function testRollingBackSpecificNumberOfMigrationsCallsDownOnThoseMigrations() : void
    {
        $migrator = new Migrator(
            ['foo', 'bar'],
            $this->connection,
            $this->migrationResolver,
            $this->executedMigrations
        );
        $this->executedMigrations->expects($this->once())
            ->method('getLast')
            ->with(1)
            ->willReturn(['bar']);
        $expectedMigration1 = $this->createMock(IMigration::class);
        $expectedMigration1->expects($this->never())
            ->method('down');
        $expectedMigration2 = $this->createMock(IMigration::class);
        $expectedMigration2->expects($this->once())
            ->method('down');
        $this->migrationResolver->expects($this->once())
            ->method('resolve')
            ->with('bar')
            ->willReturn($expectedMigration2);
        $this->connection->expects($this->once())
            ->method('beginTransaction');
        $this->connection->expects($this->once())
            ->method('commit');
        $this->assertEquals(['bar'], $migrator->rollBackMigrations(1));
    }

    /**
     * Tests that rolling back a specific number of migrations only grabs that number of migrations
     */
    public function testRollingBackSpecificNumberOfMigrationsOnlyGetsThatNumberOfMigrations() : void
    {
        $migrator = new Migrator(
            ['foo'],
            $this->connection,
            $this->migrationResolver,
            $this->executedMigrations
        );
        $this->executedMigrations->expects($this->once())
            ->method('getLast')
            ->with(2)
            ->willReturn(['foo', 'bar']);
        $migrator->rollBackMigrations(2);
    }

    /**
     * Tests that running migrations when none have been executed calls up on all
     */
    public function testRunningMigrationsWhenNoneHaveBeenExecutedCallsUpOnAll() : void
    {
        $migrator = new Migrator(
            ['foo', 'bar'],
            $this->connection,
            $this->migrationResolver,
            $this->executedMigrations
        );
        $this->executedMigrations->expects($this->once())
            ->method('getAll')
            ->willReturn([]);
        $expectedMigration1 = $this->createMock(IMigration::class);
        $expectedMigration1->expects($this->once())
            ->method('up');
        $expectedMigration2 = $this->createMock(IMigration::class);
        $expectedMigration2->expects($this->once())
            ->method('up');
        $this->migrationResolver->expects($this->at(0))
            ->method('resolve')
            ->with('foo')
            ->willReturn($expectedMigration1);
        $this->migrationResolver->expects($this->at(1))
            ->method('resolve')
            ->with('bar')
            ->willReturn($expectedMigration2);
        $this->connection->expects($this->once())
            ->method('beginTransaction');
        $this->connection->expects($this->once())
            ->method('commit');
        $this->assertEquals(['foo', 'bar'], $migrator->runMigrations());
    }

    /**
     * Tests that running only un-executed migrations are run
     */
    public function testRunningMigrationsOnlyExecutesUnExecutedMigrations() : void
    {
        $migrator = new Migrator(
            ['foo', 'bar'],
            $this->connection,
            $this->migrationResolver,
            $this->executedMigrations
        );
        // 'bar' hasn't been run
        $this->executedMigrations->expects($this->once())
            ->method('getAll')
            ->willReturn(['foo']);
        $expectedMigration1 = $this->createMock(IMigration::class);
        $expectedMigration1->expects($this->never())
            ->method('up');
        $expectedMigration2 = $this->createMock(IMigration::class);
        $expectedMigration2->expects($this->once())
            ->method('up');
        // Since 'bar' is the only migration not run, it's the only migration that gets resolved
        $this->migrationResolver->expects($this->at(0))
            ->method('resolve')
            ->with('bar')
            ->willReturn($expectedMigration2);
        $this->connection->expects($this->once())
            ->method('beginTransaction');
        $this->connection->expects($this->once())
            ->method('commit');
        $this->assertEquals(['bar'], $migrator->runMigrations());
    }

    /**
     * Tests that running only un-executed migrations are run
     */
    public function testRunningMigrationsReturnsEarlyOnError() : void
    {
        $exceptionStub = new \Exception();

        $this->expectExceptionObject($exceptionStub);

        $migrator = new Migrator(
            ['foo', 'bar'],
            $this->connection,
            $this->migrationResolver,
            $this->executedMigrations
        );
        $this->executedMigrations->expects($this->once())
            ->method('getAll')
            ->willReturn([]);
        $expectedMigration1 = $this->createMock(IMigration::class);
        $expectedMigration1->expects($this->once())
            ->method('up')
            ->willThrowException($exceptionStub);
        $expectedMigration2 = $this->createMock(IMigration::class);
        $expectedMigration2->expects($this->never())
            ->method('up');
        $this->migrationResolver->expects($this->at(0))
            ->method('resolve')
            ->with('foo')
            ->willReturn($expectedMigration1);
        $this->migrationResolver->expects($this->at(1))
            ->method('resolve')
            ->with('bar')
            ->willReturn($expectedMigration2);
        $this->connection->expects($this->once())
            ->method('beginTransaction');
        $this->connection->expects($this->once())
            ->method('rollBack');
        $this->connection->expects($this->never())
            ->method('commit');

        $migrator->runMigrations();
    }

    /**
     * Tests that running only un-executed migrations are run
     */
    public function testRollingBackAllMigrationsReturnsEarlyOnError() : void
    {
        $exceptionStub = new \Exception();

        $this->expectExceptionObject($exceptionStub);

        $migrator = new Migrator(
            ['foo', 'bar'],
            $this->connection,
            $this->migrationResolver,
            $this->executedMigrations
        );
        $this->executedMigrations->expects($this->once())
            ->method('getAll')
            ->willReturn(['foo', 'bar']);
        $expectedMigration1 = $this->createMock(IMigration::class);
        $expectedMigration1->expects($this->once())
            ->method('down')
            ->willThrowException($exceptionStub);
        $expectedMigration2 = $this->createMock(IMigration::class);
        $expectedMigration2->expects($this->never())
            ->method('down');
        $this->migrationResolver->expects($this->at(0))
            ->method('resolve')
            ->with('foo')
            ->willReturn($expectedMigration1);
        $this->migrationResolver->expects($this->at(1))
            ->method('resolve')
            ->with('bar')
            ->willReturn($expectedMigration2);
        $this->connection->expects($this->once())
            ->method('beginTransaction');
        $this->connection->expects($this->once())
            ->method('rollBack');
        $this->connection->expects($this->never())
            ->method('commit');

        $migrator->rollBackAllMigrations();
    }
}
