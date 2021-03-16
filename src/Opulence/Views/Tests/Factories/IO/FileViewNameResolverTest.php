<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Views\Tests\Factories\IO;

use InvalidArgumentException;
use Opulence\Views\Factories\IO\FileViewNameResolver;

/**
 * Tests the file view name resolver
 */
class FileViewNameResolverTest extends \PHPUnit\Framework\TestCase
{
    /** @var FileViewNameResolver The resolver to use in tests */
    private $resolver = null;

    /**
     * Sets up files before any of the tests are run
     */
    public static function setUpBeforeClass() : void
    {
        $tmpDir = self::getTmpFilePath();
        $tmpSubDir = self::getTmpFileSubDirPath();
        mkdir($tmpDir);
        mkdir($tmpSubDir);
        $files = [
            'a.php',
            'b.php',
            'a.fortune',
            'b.fortune',
            'a.fortune.php',
            'b.fortune.php'
        ];

        foreach ($files as $file) {
            file_put_contents($tmpDir . '/' . $file, $file);
            file_put_contents($tmpSubDir . '/' . $file, $file);
        }
    }

    /**
     * Deletes files after the tests are run
     */
    public static function tearDownAfterClass() : void
    {
        $files = glob(self::getTmpFilePath() . '/*');

        foreach ($files as $file) {
            if (is_dir($file)) {
                $subDirFiles = glob($file . '/*');

                foreach ($subDirFiles as $subDirFile) {
                    unlink($subDirFile);
                }

                rmdir($file);
            } else {
                unlink($file);
            }
        }

        rmdir(self::getTmpFilePath());
    }

    /**
     * Gets the path to the files in the temporary directory
     *
     * @return string The path to the files
     */
    private static function getTmpFilePath()
    {
        return __DIR__ . '/tmp';
    }

    /**
     * Gets the path to the files in a subdirectory of the temporary directory
     *
     * @return string The path to the files
     */
    private static function getTmpFileSubDirPath()
    {
        return __DIR__ . '/tmp/sub';
    }

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->resolver = new FileViewNameResolver();
    }

    /**
     * Tests that appended slashes are stripped from registered paths
     */
    public function testAppendedSlashesAreStrippedFromPaths()
    {
        $this->resolver->registerExtension('php');
        $this->resolver->registerPath(self::getTmpFilePath() . '/');
        $this->assertEquals(self::getTmpFilePath() . '/a.php', $this->resolver->resolve('a'));
    }

    /**
     * Tests that an exception is thrown when no view is found
     */
    public function testExceptionThrownWhenNoViewFound()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->resolver->registerExtension('php');
        $this->resolver->registerPath(self::getTmpFilePath());
        $this->resolver->resolve('doesNotExist');
    }

    /**
     * Tests that prepended dots are stripped from registered extensions
     */
    public function testPrependedDotsAreStrippedFromExtensions()
    {
        $this->resolver->registerExtension('.php');
        $this->resolver->registerPath(self::getTmpFilePath());
        $this->assertEquals(self::getTmpFilePath() . '/a.php', $this->resolver->resolve('a'));
    }

    /**
     * Tests registering a non-priority extension
     */
    public function testRegisteringNonPriorityExtension()
    {
        $this->resolver->registerExtension('php');
        $this->resolver->registerPath(self::getTmpFilePath());
        $this->assertEquals(self::getTmpFilePath() . '/a.php', $this->resolver->resolve('a'));
        $this->resolver->registerExtension('fortune');
        $this->assertEquals(self::getTmpFilePath() . '/a.php', $this->resolver->resolve('a'));
    }

    /**
     * Tests registering a non-priority path
     */
    public function testRegisteringNonPriorityPath()
    {
        $this->resolver->registerExtension('php');
        $this->resolver->registerPath(self::getTmpFileSubDirPath());
        $this->assertEquals(self::getTmpFileSubDirPath() . '/a.php', $this->resolver->resolve('a'));
        $this->resolver->registerPath(self::getTmpFilePath());
        $this->assertEquals(self::getTmpFileSubDirPath() . '/a.php', $this->resolver->resolve('a'));
    }

    /**
     * Tests registering a priority extension
     */
    public function testRegisteringPriorityExtension()
    {
        $this->resolver->registerExtension('php', 2);
        $this->resolver->registerPath(self::getTmpFilePath());
        $this->assertEquals(self::getTmpFilePath() . '/a.php', $this->resolver->resolve('a'));
        $this->resolver->registerExtension('fortune', 1);
        $this->assertEquals(self::getTmpFilePath() . '/a.fortune', $this->resolver->resolve('a'));
    }

    /**
     * Tests registering a priority path
     */
    public function testRegisteringPriorityPath()
    {
        $this->resolver->registerExtension('php');
        $this->resolver->registerPath(self::getTmpFileSubDirPath(), 2);
        $this->assertEquals(self::getTmpFileSubDirPath() . '/a.php', $this->resolver->resolve('a'));
        $this->resolver->registerPath(self::getTmpFilePath(), 1);
        $this->assertEquals(self::getTmpFilePath() . '/a.php', $this->resolver->resolve('a'));
    }

    /**
     * Tests resolving name with an extension
     */
    public function testResolvingNameWithExtension()
    {
        $this->resolver->registerExtension('php');
        $this->resolver->registerExtension('fortune');
        $this->resolver->registerPath(self::getTmpFilePath());
        $this->assertEquals(self::getTmpFilePath() . '/a.fortune', $this->resolver->resolve('a.fortune'));
    }

    /**
     * Tests resolving name with an extension
     */
    public function testResolvingWithExtensionsThatAreSubstringsOfOthers()
    {
        $this->resolver->registerExtension('fortune.php');
        $this->resolver->registerExtension('php');
        $this->resolver->registerPath(self::getTmpFilePath());
        $this->assertEquals(self::getTmpFilePath() . '/a.fortune.php', $this->resolver->resolve('a'));
    }
}
