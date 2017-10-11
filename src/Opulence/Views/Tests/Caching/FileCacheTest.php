<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Views\Tests\Caching;

use Opulence\IO\FileSystem;
use Opulence\Views\Caching\FileCache;
use Opulence\Views\IView;

/**
 * Tests the view cache
 */
class FileCacheTest extends \PHPUnit\Framework\TestCase
{
    /** @var FileSystem The file system to use to read cached views */
    private $fileSystem = null;
    /** @var FileCache The cache to use in tests */
    private $cache = null;
    /** @var IView|\PHPUnit_Framework_MockObject_MockObject The view to use in tests */
    private $view = null;

    /**
     * Does some setup before any tests
     */
    public static function setUpBeforeClass()
    {
        if (!is_dir(__DIR__ . '/tmp')) {
            mkdir(__DIR__ . '/tmp');
        }
    }

    /**
     * Performs some garbage collection
     */
    public static function tearDownAfterClass()
    {
        $files = glob(__DIR__ . '/tmp/*');

        foreach ($files as $file) {
            is_dir($file) ? rmdir($file) : unlink($file);
        }

        @unlink(__DIR__ . '/tmp/.gitignore');
        rmdir(__DIR__ . '/tmp');
    }

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->fileSystem = new FileSystem();
        $this->cache = new FileCache(__DIR__ . '/tmp', 3600);
        $this->view = $this->createMock(IView::class);
    }

    /**
     * Tests caching a view with a non-positive lifetime
     */
    public function testCachingWithNonPositiveLifetime()
    {
        $this->cache = new FileCache(__DIR__ . '/tmp', 0);
        $this->setViewContentsAndVars('foo', ['bar' => 'baz']);
        $this->cache->set($this->view, 'compiled', true);
        $this->assertFalse($this->cache->has($this->view, true));
        $this->assertNull($this->cache->get($this->view, true));
    }

    /**
     * Tests checking for a view that does exist
     */
    public function testCheckingForExistingView()
    {
        $this->setViewContentsAndVars('foo', ['bar' => 'baz']);
        $this->cache->set($this->view, 'compiled', true);
        $this->assertTrue($this->cache->has($this->view, true));
        $this->assertEquals('compiled', $this->cache->get($this->view, true));
    }

    /**
     * Tests checking for a view that exists but doesn't match on variables
     */
    public function testCheckingForExistingViewWithNoVariableMatches()
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

    /**
     * Tests checking for a view that exists but doesn't match on variables when ignoring view variables' values
     */
    public function testCheckingForExistingViewWithNoVariableMatchesWhenIgnoringViewVariablesValues()
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

    /**
     * Tests checking for an expired view
     */
    public function testCheckingForExpiredView()
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
    public function testCheckingForNonExistentView()
    {
        $this->setViewContentsAndVars('this-content-does-not-exist', []);
        $this->assertFalse($this->cache->has($this->view, true));
        $this->assertNull($this->cache->get($this->view, true));
    }

    /**
     * Tests flushing cache
     */
    public function testFlushingCache()
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

    /**
     * Tests running garbage collection
     */
    public function testGarbageCollection()
    {
        $this->fileSystem->write(__DIR__ . '/tmp/foo', 'compiled');
        $this->cache = new FileCache(__DIR__ . '/tmp', -1);
        $this->cache->gc();
        $this->assertEquals([], $this->fileSystem->getFiles(__DIR__ . '/tmp'));
    }

    /**
     * Tests that .gitignore files are kept during flushing
     */
    public function testGitignoreIsKeptDuringFlush()
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
    public function testGitignoreIsKeptDuringGC()
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

    /**
     * Tests not creating a directory before attempting to cache views in it
     */
    public function testNotCreatingDirectoryBeforeCaching()
    {
        $this->cache = new FileCache(__DIR__ . '/verytemporarytmp', 3600);
        $this->setViewContentsAndVars('foo', ['bar' => 'baz']);
        $this->cache->set($this->view, 'compiled');
        $this->assertTrue($this->cache->has($this->view));
        // Cleanup this cache
        $this->cache->flush();
    }

    /**
     * Tests setting a path and checking for a view
     */
    public function testSettingPathCheckingForExistingView()
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
    private function setViewContentsAndVars($contents, array $vars)
    {
        $this->view->expects($this->any())
            ->method('getContents')
            ->willReturn($contents);
        $this->view->expects($this->any())
            ->method('getVars')
            ->willReturn($vars);
    }
}
