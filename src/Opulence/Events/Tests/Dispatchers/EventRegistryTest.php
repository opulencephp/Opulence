<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Events\Tests\Dispatchers;

use Opulence\Events\Dispatchers\EventRegistry;
use Opulence\Events\Tests\Mocks\Event;
use Opulence\Events\Tests\Mocks\Listener;

/**
 * Tests the event registry
 */
class EventRegistryTest extends \PHPUnit\Framework\TestCase
{
    /** @var EventRegistry The registry to use in tests */
    private $eventRegistry = null;
    /** @var Event The event to use in tests */
    private $event = null;
    /** @var Listener|\PHPUnit_Framework_MockObject_MockObject The mock listener to use in tests */
    private $listener = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->eventRegistry = new EventRegistry();
        $this->event = new Event();
        $this->listener = $this->createMock(Listener::class);
    }

    /**
     * Tests adding listeners
     */
    public function testAddingListeners()
    {
        $listener1 = [$this->listener, 'doNothing1'];
        $listener2 = [$this->listener, 'doNothing2'];
        $this->eventRegistry->registerListener('foo', $listener1);
        $this->assertEquals([$listener1], $this->eventRegistry->getListeners('foo'));
        $this->assertTrue($this->eventRegistry->hasListeners('foo'));
        $this->eventRegistry->registerListener('foo', $listener2);
        $this->assertEquals([$listener1, $listener2], $this->eventRegistry->getListeners('foo'));
        $this->assertTrue($this->eventRegistry->hasListeners('foo'));
    }

    /**
     * Tests checking if an event has listeners
     */
    public function testCheckingIfEventHasListeners()
    {
        $this->assertFalse($this->eventRegistry->hasListeners('foo'));
        $this->eventRegistry->registerListener('foo', [$this->listener, 'doNothing1']);
        $this->assertTrue($this->eventRegistry->hasListeners('foo'));
    }

    /**
     * Tests getting listeners
     */
    public function testGettingListeners()
    {
        $this->assertEquals([], $this->eventRegistry->getListeners('foo'));
        $listener1 = [$this->listener, 'doNothing1'];
        $listener2 = [$this->listener, 'doNothing2'];
        $this->eventRegistry->registerListener('foo', $listener1);
        $this->eventRegistry->registerListener('foo', $listener2);
        $this->assertEquals([$listener1, $listener2], $this->eventRegistry->getListeners('foo'));
    }

    /**
     * Test that a listener cannot be added twice
     */
    public function testListenerCannotBeAddedTwice()
    {
        $listener = [$this->listener, 'doNothing1'];
        $this->eventRegistry->registerListener('foo', $listener);
        $this->eventRegistry->registerListener('foo', $listener);
        $this->assertEquals([$listener], $this->eventRegistry->getListeners('foo'));
    }

    /**
     * Tests removing listeners
     */
    public function testRemovingListeners()
    {
        $listener1 = [$this->listener, 'doNothing1'];
        $listener2 = [$this->listener, 'doNothing2'];
        $this->eventRegistry->registerListener('foo', $listener1);
        $this->eventRegistry->registerListener('foo', $listener2);
        $this->eventRegistry->removeListener('foo', $listener2);
        $this->assertEquals([$listener1], $this->eventRegistry->getListeners('foo'));
        $this->assertTrue($this->eventRegistry->hasListeners('foo'));
        $this->eventRegistry->removeListener('foo', $listener1);
        $this->assertEquals([], $this->eventRegistry->getListeners('foo'));
        $this->assertFalse($this->eventRegistry->hasListeners('foo'));
    }
}
