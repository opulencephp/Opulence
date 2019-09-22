<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Tests\Composer;

use Opulence\Framework\Composer\Composer;

/**
 * Tests the Composer wrapper
 */
class ComposerTest extends \PHPUnit\Framework\TestCase
{
    /** @var array A fully-loaded Composer config */
    private static array $fullyLoadedConfig = [
        'name' => '__name__',
        'description' => '__description__',
        'keywords' => ['__keyword1__', '__keyword2__'],
        'authors' => [
            ['name' => 'Dave', 'email' => 'foo@bar.com']
        ],
        'license' => '__license__',
        'autoload' => [
            'psr-4' => [
                '__namespace__' => [
                    '__namespacepath1__',
                    '__namespacepath2__'
                ],
                'Opulence\\' => [
                    'src/Opulence',
                    'tests/src/Opulence'
                ]
            ]
        ]
    ];
    private Composer $composer;
    private string $rootPath = '';
    private string $psr4RootPath = '';

    protected function setUp(): void
    {
        $this->rootPath = realpath(__DIR__ . '/../../../../..');
        $this->psr4RootPath = realpath(__DIR__ . '/../../../../../src');
        $this->composer = new Composer(self::$fullyLoadedConfig, $this->rootPath, $this->psr4RootPath);
    }

    public function testCreatingConfigFromRawConfig(): void
    {
        $composer = Composer::createFromRawConfig($this->rootPath, $this->psr4RootPath);
        $composerFileContents = json_decode(file_get_contents($this->rootPath . '/composer.json'), true);
        $this->assertEquals($composerFileContents, $composer->getRawConfig());
    }

    public function testGettingFullyQualifiedClassName(): void
    {
        $this->assertEquals('Opulence\\Bar', $this->composer->getFullyQualifiedClassName('Bar', 'Opulence'));
        $this->assertEquals('Opulence\\Foo\\Bar', $this->composer->getFullyQualifiedClassName('Bar', 'Opulence\\Foo'));
        $this->assertEquals('Opulence\\Bar', $this->composer->getFullyQualifiedClassName('Bar', 'Opulence\\'));
    }

    /**
     * Tests getting the fully qualified class name of a fully-qualified class
     */
    public function testGettingFullyQualifiedClassNameOfAFullyQualifiedClass(): void
    {
        $this->assertEquals(
            'Opulence\\Foo\\Bar',
            $this->composer->getFullyQualifiedClassName('Opulence\\Foo\\Bar', 'Opulence')
        );
    }

    public function testGettingMultiDimensionalProperty(): void
    {
        $this->assertEquals(
            ['__namespacepath1__', '__namespacepath2__'],
            $this->composer->get('autoload.psr-4.__namespace__')
        );
    }

    /**
     * Tests getting a non-existent multi-dimensional property
     */
    public function testGettingNonExistentMultiDimensionalProperty(): void
    {
        $this->assertNull($this->composer->get('autoload.psr-4.doesNotExist'));
    }

    /**
     * Tests getting a non-existent root namespace
     */
    public function testGettingNonExistentRootNamespace(): void
    {
        $composer = new Composer(['foo' => 'bar'], $this->rootPath, $this->psr4RootPath);
        $this->assertNull($composer->getRootNamespace());
    }

    /**
     * Tests getting a non-existent root namespace paths
     */
    public function testGettingNonExistentRootNamespacePaths(): void
    {
        $composer = new Composer(['foo' => 'bar'], $this->rootPath, $this->psr4RootPath);
        $this->assertNull($composer->getRootNamespacePaths());
    }

    /**
     * Tests getting a non-existent single property
     */
    public function testGettingNonExistentSingleProperty(): void
    {
        $this->assertNull($this->composer->get('doesNotExist'));
    }

    public function testGettingPathFromClass(): void
    {
        $class = 'Opulence\\Foo\\Bar';
        $this->assertEquals(
            $this->psr4RootPath . DIRECTORY_SEPARATOR . 'Opulence' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Bar.php',
            $this->composer->getClassPath($class)
        );
    }

    public function testGettingPathFromClassInRootNamespace(): void
    {
        $class = 'Opulence\\Bar';
        $this->assertEquals(
            $this->psr4RootPath . DIRECTORY_SEPARATOR . 'Opulence' . DIRECTORY_SEPARATOR . 'Bar.php',
            $this->composer->getClassPath($class)
        );
    }

    public function testGettingRawConfig(): void
    {
        $this->assertEquals(self::$fullyLoadedConfig, $this->composer->getRawConfig());
        // As a sanity check, make sure it's not an empty array
        $this->assertNotEquals([], $this->composer->getRawConfig());
    }

    /**
     * Tests getting the raw config from non-existent Composer config
     */
    public function testGettingRawConfigFromNonExistentComposerConfig(): void
    {
        $composer = Composer::createFromRawConfig(__DIR__, $this->psr4RootPath);
        $this->assertEquals([], $composer->getRawConfig());
    }

    public function testGettingRootNamespace(): void
    {
        $this->assertEquals('Opulence', $this->composer->getRootNamespace());
    }

    public function testGettingRootNamespacePaths(): void
    {
        $this->assertEquals(['src/Opulence', 'tests/src/Opulence'], $this->composer->getRootNamespacePaths());
    }

    public function testGettingRootNamespacePathsWithStringNamespace(): void
    {
        $composer = new Composer(
            ['autoload' => ['psr-4' => ['Opulence\\' => 'src/Opulence']]],
            $this->rootPath,
            $this->psr4RootPath
        );
        $this->assertEquals(['src/Opulence'], $composer->getRootNamespacePaths());
    }

    public function testGettingRootNamespaceWithStringNamespace(): void
    {
        $composer = new Composer(
            ['autoload' => ['psr-4' => ['Opulence\\' => 'src/Opulence']]],
            $this->rootPath,
            $this->psr4RootPath
        );
        $this->assertEquals('Opulence', $composer->getRootNamespace());
    }

    public function testGettingSingleProperty(): void
    {
        $this->assertEquals('__name__', $this->composer->get('name'));
    }
}
