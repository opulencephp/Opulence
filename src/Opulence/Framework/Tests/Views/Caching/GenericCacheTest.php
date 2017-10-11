<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Tests\Views\Caching;

use Opulence\Cache\ICacheBridge;
use Opulence\Framework\Views\Caching\GenericCache;
use Opulence\Views\IView;

/**
 * Tests the generic view cache
 */
class GenericCacheTest extends \PHPUnit\Framework\TestCase
{
    /** @var ICacheBridge|\PHPUnit_Framework_MockObject_MockObject The caching bridge to use in tests */
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
        $this->bridge = $this->createMock(ICacheBridge::class);
        $this->cache = new GenericCache($this->bridge, 3600);
        $this->view = $this->createMock(IView::class);
    }

    /**
     * Tests checking for an existing view
     */
    public function testCheckingForExistingView()
    {
        $this->bridge->expects($this->once())
            ->method('get')
            ->with($this->getKey($this->view))
            ->willReturn('compiled');
        $this->bridge->expects($this->once())
            ->method('has')
            ->with($this->getKey($this->view))
            ->willReturn(true);
        $this->assertEquals('compiled', $this->cache->get($this->view));
        $this->assertTrue($this->cache->has($this->view));
    }

    /**
     * Tests checking for a non-existent view
     */
    public function testCheckingForNonExistentView()
    {
        $this->bridge->expects($this->once())
            ->method('get')
            ->with($this->getKey($this->view))
            ->willReturn(null);
        $this->bridge->expects($this->once())
            ->method('has')
            ->with($this->getKey($this->view))
            ->willReturn(false);
        $this->assertNull($this->cache->get($this->view));
        $this->assertFalse($this->cache->has($this->view));
    }

    /**
     * Tests flushing cache
     */
    public function testFlushingCache()
    {
        $this->bridge->expects($this->once())
            ->method('flush');
        $this->cache->flush();
    }

    /**
     * Tests getting the view
     */
    public function testGettingView()
    {
        $this->bridge->expects($this->once())
            ->method('get')
            ->with($this->getKey($this->view))
            ->willReturn('compiled');
        $this->assertEquals('compiled', $this->cache->get($this->view));
    }

    /**
     * Tests setting the view
     */
    public function testSettingView()
    {
        $this->bridge->expects($this->once())
            ->method('set')
            ->with($this->getKey($this->view), 'compiled', 3600);
        $this->cache->set($this->view, 'compiled');
    }

    /**
     * Gets the key for the cached view
     *
     * @param IView $view The view whose cache key we want
     * @return string The key for the cached view
     */
    private function getKey(IView $view) : string
    {
        return md5(http_build_query([
            'u' => $view->getContents(),
            'v' => $view->getVars()
        ]));
    }
}
