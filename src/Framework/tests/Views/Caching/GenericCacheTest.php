<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Tests\Views\Caching;

use Opulence\Cache\ICacheBridge;
use Opulence\Framework\Views\Caching\GenericCache;
use Opulence\Views\IView;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the generic view cache
 */
class GenericCacheTest extends TestCase
{
    /** @var ICacheBridge|MockObject The caching bridge to use in tests */
    private ICacheBridge $bridge;
    /** @var GenericCache The cache to use in tests */
    private GenericCache $cache;
    /** @var IView|MockObject The view to use in tests */
    private IView $view;

    protected function setUp(): void
    {
        $this->bridge = $this->createMock(ICacheBridge::class);
        $this->cache = new GenericCache($this->bridge, 3600);
        $this->view = $this->createMock(IView::class);
    }

    public function testCheckingForExistingView(): void
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
    public function testCheckingForNonExistentView(): void
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

    public function testFlushingCache(): void
    {
        $this->bridge->expects($this->once())
            ->method('flush');
        $this->cache->flush();
    }

    public function testGettingView(): void
    {
        $this->bridge->expects($this->once())
            ->method('get')
            ->with($this->getKey($this->view))
            ->willReturn('compiled');
        $this->assertEquals('compiled', $this->cache->get($this->view));
    }

    public function testSettingView(): void
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
    private function getKey(IView $view): string
    {
        return md5(http_build_query([
            'u' => $view->getContents(),
            'v' => $view->getVars()
        ]));
    }
}
