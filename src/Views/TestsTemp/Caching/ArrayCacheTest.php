<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\TestsTemp\Caching;

use Opulence\Views\Caching\ArrayCache;
use Opulence\Views\IView;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the view array cache
 */
class ArrayCacheTest extends \PHPUnit\Framework\TestCase
{
    private ArrayCache $cache;
    /** @var IView|MockObject The view to use in tests */
    private IView $view;

    protected function setUp(): void
    {
        $this->cache = new ArrayCache();
        $this->view = $this->createMock(IView::class);
    }

    public function testFlushingCacheRemovesViews(): void
    {
        $this->cache->set($this->view, 'foo');
        $this->cache->flush();
        $this->assertNull($this->cache->get($this->view));
        $this->assertFalse($this->cache->has($this->view));
    }

    public function testGettingExistingView(): void
    {
        $this->cache->set($this->view, 'foo');
        $this->assertEquals('foo', $this->cache->get($this->view));
        $this->assertTrue($this->cache->has($this->view));
    }

    /**
     * Tests getting a non-existent view
     */
    public function testGettingNonExistentView(): void
    {
        $this->assertNull($this->cache->get($this->view));
        $this->assertFalse($this->cache->has($this->view));
    }

    public function testGettingViewsWithSameContentButDifferentViewVariables(): void
    {
        $view1 = $this->createMock(IView::class);
        $view2 = $this->createMock(IView::class);
        $view1->expects($this->any())
            ->method('getContents')
            ->willReturn('foo');
        $view2->expects($this->any())
            ->method('getContents')
            ->willReturn('foo');
        $view1->expects($this->any())
            ->method('getVars')
            ->willReturn(['bar' => 'baz']);
        $view2->expects($this->any())
            ->method('getVars')
            ->willReturn(['bar' => 'blah']);
        $this->cache->set($view1, 'content');
        $this->assertEquals('content', $this->cache->get($view1));
        $this->assertEquals('content', $this->cache->get($view2));
        $this->assertTrue($this->cache->has($view1));
        $this->assertTrue($this->cache->has($view2));
    }
}
