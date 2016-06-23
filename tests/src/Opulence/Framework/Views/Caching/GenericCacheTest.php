<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Views\Caching;

use Opulence\Cache\ArrayBridge;
use Opulence\Files\FileSystem;
use Opulence\Views\IView;

/**
 * Tests the generic view cache
 */
class GenericCacheTest extends \PHPUnit\Framework\TestCase
{
    /** @var ArrayBridge The caching bridge to use in tests */
    private $bridge = null;
    /** @var GenericCache The cache to use in tests */
    private $cache = null;
    /** @var IView|\PHPUnit_Framework_MockObject_MockObject The view to use in tests */
    private $view = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->bridge = new ArrayBridge();
        $this->cache = new GenericCache($this->bridge, 3600);
        $this->view = $this->createMock(IView::class);
    }

    /**
     * Tests caching a view with a non-positive lifetime
     */
    public function testCachingWithNonPositiveLifetime()
    {
        $this->bridge = new ArrayBridge();
        $this->cache = new GenericCache($this->bridge, 0);
        $this->setViewContentsAndVars("foo", ["bar" => "baz"]);
        $this->cache->set($this->view, "compiled");
        $this->assertFalse($this->cache->has($this->view));
        $this->assertNull($this->cache->get($this->view));
    }

    /**
     * Tests checking for a view that does exist
     */
    public function testCheckingForExistingView()
    {
        $this->setViewContentsAndVars("foo", ["bar" => "baz"]);
        $this->cache->set($this->view, "compiled");
        $this->assertTrue($this->cache->has($this->view));
        $this->assertEquals("compiled", $this->cache->get($this->view));
    }

    /**
     * Tests checking for a view that exists but doesn't match on variables
     */
    public function testCheckingForExistingViewWithNoVariableMatches()
    {
        $this->view->expects($this->any())
            ->method("getContents")
            ->willReturn("foo");
        $this->view->expects($this->at(0))
            ->method("getVars")
            ->willReturn(["bar" => "baz"]);
        $this->view->expects($this->at(1))
            ->method("getVars")
            ->willReturn(["wrong" => "ahh"]);
        $this->cache->set($this->view, "compiled");
        $this->assertFalse($this->cache->has($this->view));
    }

    /**
     * Tests checking for an expired view
     */
    public function testCheckingForExpiredView()
    {
        // The negative expiration is a way of forcing everything to expire right away
        $cache = new GenericCache($this->bridge, -1);
        $this->setViewContentsAndVars("foo", ["bar" => "baz"]);
        $cache->set($this->view, "compiled");
        $this->assertFalse($cache->has($this->view));
        $this->assertNull($cache->get($this->view));
    }

    /**
     * Tests checking for a non-existent view
     */
    public function testCheckingForNonExistentView()
    {
        $this->setViewContentsAndVars("foo", []);
        $this->assertFalse($this->cache->has($this->view));
        $this->assertNull($this->cache->get($this->view));
    }

    /**
     * Tests flushing cache
     */
    public function testFlushingCache()
    {
        $this->view->expects($this->any())
            ->method("getContents")
            ->willReturn("foo");
        $this->view->expects($this->at(0))
            ->method("getVars")
            ->willReturn(["bar1" => "baz"]);
        $this->view->expects($this->at(1))
            ->method("getVars")
            ->willReturn(["bar1" => "baz"]);
        $this->view->expects($this->at(2))
            ->method("getVars")
            ->willReturn(["bar2" => "baz"]);
        $this->view->expects($this->at(3))
            ->method("getVars")
            ->willReturn(["bar2" => "baz"]);
        $this->cache->set($this->view, "compiled1");
        $this->cache->set($this->view, "compiled2");
        $this->cache->flush();
        $this->assertFalse($this->cache->has($this->view));
        $this->assertFalse($this->cache->has($this->view));
    }

    /**
     * Tests not creating a directory before attempting to cache views in it
     */
    public function testNotCreatingDirectoryBeforeCaching()
    {
        $this->cache = new GenericCache($this->bridge, 3600);
        $this->setViewContentsAndVars("foo", ["bar" => "baz"]);
        $this->cache->set($this->view, "compiled");
        $this->assertTrue($this->cache->has($this->view));
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
            ->method("getContents")
            ->willReturn($contents);
        $this->view->expects($this->any())
            ->method("getVars")
            ->willReturn($vars);
    }
}
