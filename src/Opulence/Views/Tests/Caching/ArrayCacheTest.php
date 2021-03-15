<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Views\Tests\Caching;

use Opulence\Views\Caching\ArrayCache;
use Opulence\Views\IView;

/**
 * Tests the view array cache
 */
class ArrayCacheTest extends \PHPUnit\Framework\TestCase
{
    /** @var ArrayCache The cache to use in tests */
    private $cache = null;
    /** @var IView|\PHPUnit_Framework_MockObject_MockObject The view to use in tests */
    private $view = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->cache = new ArrayCache();
        $this->view = $this->createMock(IView::class);
    }

    /**
     * Tests flushing the cache removes views
     */
    public function testFlushingCacheRemovesViews()
    {
        $this->cache->set($this->view, 'foo');
        $this->cache->flush();
        $this->assertNull($this->cache->get($this->view));
        $this->assertFalse($this->cache->has($this->view));
    }

    /**
     * Tests getting an existing view
     */
    public function testGettingExistingView()
    {
        $this->cache->set($this->view, 'foo');
        $this->assertEquals('foo', $this->cache->get($this->view));
        $this->assertTrue($this->cache->has($this->view));
    }

    /**
     * Tests getting a non-existent view
     */
    public function testGettingNonExistentView()
    {
        $this->assertNull($this->cache->get($this->view));
        $this->assertFalse($this->cache->has($this->view));
    }

    /**
     * Tests getting views with the same content but different view variables
     */
    public function testGettingViewsWithSameContentButDifferentViewVariables()
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
