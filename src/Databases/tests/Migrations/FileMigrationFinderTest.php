<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\Tests\Migrations;

use InvalidArgumentException;
use Opulence\Databases\Migrations\FileMigrationFinder;
use Opulence\Databases\Tests\Migrations\Mocks\MigrationA;
use Opulence\Databases\Tests\Migrations\Mocks\MigrationB;
use Opulence\Databases\Tests\Migrations\Mocks\Subdirectory\MigrationC;
use PHPUnit\Framework\TestCase;

/**
 * Tests the migration finder
 */
class FileMigrationFinderTest extends TestCase
{
    /** @var string The directory that holds our migrations */
    private const MIGRATION_DIRECTORY = __DIR__ . '/Mocks';
    /** @var FileMigrationFinder The finder to use in tests */
    private FileMigrationFinder $migrationFinder;
    /** @var string The name of the namespace the top-level migrations belong to */
    private string $topLevelMigrationNamespace = '';

    protected function setUp(): void
    {
        $this->migrationFinder = new FileMigrationFinder();
        $topLevelMigrationNamePieces = explode('\\', MigrationA::class);
        $this->topLevelMigrationNamespace = implode(
            '\\',
            array_slice($topLevelMigrationNamePieces, 0, -1)
        );
    }

    public function testMigrationsAreFoundInChildlessDirectory(): void
    {
        $expectedMigrations = [MigrationC::class];
        $this->assertEquals(
            $expectedMigrations,
            $this->migrationFinder->findAll(self::MIGRATION_DIRECTORY . '/Subdirectory')
        );
    }

    public function testMigrationsAreFoundInSubdirectories(): void
    {
        $expectedMigrations = [
            MigrationC::class,
            MigrationA::class,
            MigrationB::class,
        ];
        $this->assertEquals(
            $expectedMigrations,
            $this->migrationFinder->findAll(self::MIGRATION_DIRECTORY)
        );
    }

    /**
     * Tests that non-directory paths throw an exception
     */
    public function testNonDirectoryPathThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->migrationFinder->findAll(__FILE__);
    }
}
