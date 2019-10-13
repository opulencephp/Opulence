<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Tests\Caching;

use Aphiria\IO\FileSystem;
use Opulence\Views\Caching\FileCache;
use Opulence\Views\IView;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the view cache
 */
class FileCacheTest extends \PHPUnit\Framework\TestCase
{
    private FileSystem $fileSystem;
    private FileCache $cache;
    /** @var IView|MockObject The view to use in tests */
    private IView $view;

    public static function setUpBeforeClass(): void
    {
        if (!is_dir(__DIR__ . '/tmp')) {
            mkdir(__DIR__ . '/tmp');
        }
    }

    public static function tearDownAfterClass(): void
    {
        $files = glob(__DIR__ . '/tmp/*');

        foreach ($files as $file) {
            is_dir($file) ? rmdir($file) : unlink($file);
        }

        @unlink(__DIR__ . '/tmp/.gitignore');
        rmdir(__DIR__ . '/tmp');
    }

    protected function setUp(): void
    {
        $this->fileSystem = new FileSystem();
        $this->cache = new FileCache(__DIR__ . '/tmp', 3600);
        $this->view = $this->createMock(IView::class);
    }

    /**
     * Tests caching a view with a non-positive lifetime
     */
    public function testCachingWithNonPositiveLifetime(): void
    {
        $this->cache = new FileCache(__DIR__ . '/tmp', 0);
        $this->setViewContentsAndVars('foo', ['bar' => 'baz']);
        $this->cache->set($this->view, 'compiled', true);
        $this->assertFalse($this->cache->has($this->view, true));
        $this->assertNull($this->cache->get($this->view, true));
    }

    public function testCheckingForExistingView(): void
    {
        $this->setViewContentsAndVars('foo', ['bar' => 'baz']);
        $this->cache->set($this->view, 'compiled', true);
        $this->assertTrue($this->cache->has($this->view, true));
        $this->assertEquals('compiled', $this->cache->get($this->view, true));
    }

    public function testCheckingForExistingViewWithNoVariableMatches(): void
    {
        $this->view->expects($this->any())
            ->method('getContents')
            ->willReturn('foo');
        $this->view->expects($this->at(0))
            ->method('getVars')
            ->willReturn(['bar' => 'baz']);
        $this->view->expects($this->at(1))
            ->method('getVars')
            ->willReturn(['wrong' => 'ahh']);
        $this->cache->set($this->view, 'compiled', true);
        $this->assertFalse($this->cache->has($this->view, true));
    }

    public function testCheckingForExistingViewWithNoVariableMatchesWhenIgnoringViewVariablesValues(): void
    {
        $this->view->expects($this->any())
            ->method('getContents')
            ->willReturn('foo');
        $this->view->expects($this->at(0))
            ->method('getVars')
            ->willReturn(['bar' => 'baz']);
        $this->view->expects($this->at(1))
            ->method('getVars')
            ->willReturn(['wrong' => 'ahh']);
        $this->cache->set($this->view, 'compiled', false);
        $this->assertTrue($this->cache->has($this->view, false));
    }

    public function testCheckingForExpiredView(): void
    {
        // The negative expiration is a way of forcing everything to expire right away
        $cache = new FileCache(__DIR__ . '/tmp', -1);
        $this->setViewContentsAndVars('foo', ['bar' => 'baz']);
        $cache->set($this->view, 'compiled');
        $this->assertFalse($cache->has($this->view, true));
        $this->assertNull($cache->get($this->view, true));
    }

    /**
     * Tests checking for a non-existent view
     */
    public function testCheckingForNonExistentView(): void
    {
        $this->setViewContentsAndVars('this-content-does-not-exist', []);
        $this->assertFalse($this->cache->has($this->view, true));
        $this->assertNull($this->cache->get($this->view, true));
    }

    public function testFlushingCache(): void
    {
        $this->view->expects($this->any())
            ->method('getContents')
            ->willReturn('foo');
        $this->view->expects($this->at(0))
            ->method('getVars')
            ->willReturn(['bar1' => 'baz']);
        $this->view->expects($this->at(1))
            ->method('getVars')
            ->willReturn(['bar1' => 'baz']);
        $this->view->expects($this->at(2))
            ->method('getVars')
            ->willReturn(['bar2' => 'baz']);
        $this->view->expects($this->at(3))
            ->method('getVars')
            ->willReturn(['bar2' => 'baz']);
        $this->cache->set($this->view, 'compiled1');
        $this->cache->set($this->view, 'compiled2');
        $this->cache->flush();
        $this->assertFalse($this->cache->has($this->view));
        $this->assertFalse($this->cache->has($this->view));
    }

    public function testGarbageCollection(): void
    {
        $this->fileSystem->write(__DIR__ . '/tmp/foo', 'compiled');
        $this->cache = new FileCache(__DIR__ . '/tmp', -1);
        $this->cache->gc();
        $this->assertEquals([], $this->fileSystem->getFiles(__DIR__ . '/tmp'));
    }

    /**
     * Tests that .gitignore files are kept during flushing
     */
    public function testGitignoreIsKeptDuringFlush(): void
    {
        $this->fileSystem->write(__DIR__ . '/tmp/.gitignore', '');
        $this->fileSystem->write(__DIR__ . '/tmp/.gitignore_tmp', '');
        $this->fileSystem->write(__DIR__ . '/tmp/.gitkeep', '');

        $cache = new FileCache(__DIR__ . '/tmp', -1);
        $cache->flush();

        $this->assertFileExists(__DIR__ . '/tmp/.gitignore');
        $this->assertFileNotExists(__DIR__ . '/tmp/.gitignore_tmp');
        $this->assertFileNotExists(__DIR__ . '/tmp/.gitkeep');
    }

    /**
     * Tests that .gitignore files are kept during garbage collection
     */
    public function testGitignoreIsKeptDuringGC(): void
    {
        $this->fileSystem->write(__DIR__ . '/tmp/.gitignore', '');
        $this->fileSystem->write(__DIR__ . '/tmp/.gitignore_tmp', '');
        $this->fileSystem->write(__DIR__ . '/tmp/.gitkeep', '');

        $cache = new FileCache(__DIR__ . '/tmp', -1);
        $cache->gc();

        $this->assertFileExists(__DIR__ . '/tmp/.gitignore');
        $this->assertFileNotExists(__DIR__ . '/tmp/.gitignore_tmp');
        $this->assertFileNotExists(__DIR__ . '/tmp/.gitkeep');
    }

    public function testNotCreatingDirectoryBeforeCaching(): void
    {
        $this->cache = new FileCache(__DIR__ . '/verytemporarytmp', 3600);
        $this->setViewContentsAndVars('foo', ['bar' => 'baz']);
        $this->cache->set($this->view, 'compiled');
        $this->assertTrue($this->cache->has($this->view));
        // Cleanup this cache
        $this->cache->flush();
    }

    public function testSettingPathCheckingForExistingView(): void
    {
        // I know this is also done in setUp(), but we're specifically testing that it works after setting the path
        $this->cache->setPath(__DIR__ . '/tmp');
        $this->testCheckingForExistingView();
    }

    /**
     * Sets the contents and vars in a view
     *
     * @param string $contents The contents to set
     * @param array $vars The vars to set
     */
    private function setViewContentsAndVars($contents, array $vars): void
    {
        $this->view->expects($this->any())
            ->method('getContents')
            ->willReturn($contents);
        $this->view->expects($this->any())
            ->method('getVars')
            ->willReturn($vars);
    }
}
