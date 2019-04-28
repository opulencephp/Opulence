<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Tests\Bootstrappers;

use InvalidArgumentException;
use Opulence\Ioc\Bootstrappers\FileBootstrapperFinder;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\Finder\BootstrapperA;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\Finder\BootstrapperB;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\Finder\Subdirectory\BootstrapperC;
use PHPUnit\Framework\TestCase;

/**
 * Tests the file bootstrapper finder
 */
class FileBootstrapperFinderTest extends TestCase
{
    /** @var string */
    private const BOOTSTRAPPER_DIRECTORY = __DIR__ . '/Mocks/Finder';
    /** @var FileBootstrapperFinder */
    private $bootstrapperFinder;
    /** @var string */
    private $topLevelBootstrapperNamespace = '';

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->bootstrapperFinder = new FileBootstrapperFinder();
        $topLevelBootstrapperNamePieces = explode('\\', BootstrapperA::class);
        $this->topLevelBootstrapperNamespace = implode(
            '\\',
            array_slice($topLevelBootstrapperNamePieces, 0, -1)
        );
    }

    public function testBootstrappersAreFoundInChildlessDirectory(): void
    {
        $expectedBootstrappers = [BootstrapperC::class];
        $this->assertEquals(
            $expectedBootstrappers,
            $this->bootstrapperFinder->findAll(self::BOOTSTRAPPER_DIRECTORY . '/Subdirectory')
        );
    }

    public function testBootstrappersAreFoundInSubdirectories(): void
    {
        $expectedBootstrappers = [
            BootstrapperA::class,
            BootstrapperB::class,
            BootstrapperC::class,
        ];
        // We don't care so much about the ordering
        $this->assertEqualsCanonicalizing(
            $expectedBootstrappers,
            $this->bootstrapperFinder->findAll(self::BOOTSTRAPPER_DIRECTORY)
        );
    }

    public function testNonDirectoryPathThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->bootstrapperFinder->findAll(__FILE__);
    }
}
