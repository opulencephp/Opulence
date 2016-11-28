<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views\Caching;

use Opulence\Views\IView;

/**
 * Tests the view array cache
 */
class ArrayCacheTest extends \PHPUnit\Framework\TestCase
{
    /** @var FileCache The cache to use in tests */
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
        $this->cache->set($this->view, "foo");
        $this->cache->flush();
        $this->assertNull($this->cache->get($this->view));
        $this->assertFalse($this->cache->has($this->view));
    }

    /**
     * Tests getting an existing view
     */
    public function testGettingExistingView()
    {
        $this->cache->set($this->view, "foo");
        $this->assertEquals("foo", $this->cache->get($this->view));
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
}