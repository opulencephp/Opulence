<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Events\Dispatchers;

use Opulence\Events\Event;
use Opulence\Tests\Events\Mocks\Listener;

/**
 * Tests the event dispatcher
 */
class EventDispatcherTest extends \PHPUnit\Framework\TestCase
{
    /** @var EventDispatcher The dispatcher to use in tests */
    private $dispatcher = null;
    /** @var Event|\PHPUnit_Framework_MockObject_MockObject The event to use in tests */
    private $event = null;
    /** @var Listener|\PHPUnit_Framework_MockObject_MockObject The mock listener to use in tests */
    private $listener = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->dispatcher = new EventDispatcher();
        $this->event = $this->getMockForAbstractClass(Event::class);
        $this->listener = $this->createMock(Listener::class);
    }

    /**
     * Tests adding listeners
     */
    public function testAddingListeners()
    {
        $listener1 = [$this->listener, "doNothing1"];
        $listener2 = [$this->listener, "doNothing2"];
        $this->dispatcher->registerListener("foo", $listener1);
        $this->assertEquals([$listener1], $this->dispatcher->getListeners("foo"));
        $this->assertTrue($this->dispatcher->hasListeners("foo"));
        $this->dispatcher->registerListener("foo", $listener2);
        $this->assertEquals([$listener1, $listener2], $this->dispatcher->getListeners("foo"));
        $this->assertTrue($this->dispatcher->hasListeners("foo"));
    }

    /**
     * Tests checking if an event has listeners
     */
    public function testCheckingIfEventHasListeners()
    {
        $this->assertFalse($this->dispatcher->hasListeners("foo"));
        $this->dispatcher->registerListener("foo", [$this->listener, "doNothing1"]);
        $this->assertTrue($this->dispatcher->hasListeners("foo"));
    }

    /**
     * Tests dispatching to multiple listeners
     */
    public function testDispatchingToMultipleListeners()
    {
        $this->dispatcher->registerListener("foo", [$this->listener, "doNothing1"]);
        $this->dispatcher->registerListener("foo", [$this->listener, "doNothing2"]);
        $this->listener->expects($this->once())->method("doNothing1")->with($this->event, "foo", $this->dispatcher);
        $this->listener->expects($this->once())->method("doNothing2")->with($this->event, "foo", $this->dispatcher);
        $this->dispatcher->dispatch("foo", $this->event);
    }

    /**
     * Tests dispatching to a single listener
     */
    public function testDispatchingToSingleListener()
    {
        $this->dispatcher->registerListener("foo", [$this->listener, "doNothing1"]);
        $this->listener->expects($this->once())->method("doNothing1")->with($this->event, "foo", $this->dispatcher);
        $this->dispatcher->dispatch("foo", $this->event);
    }

    /**
     * Tests dispatching with no listeners
     */
    public function testDispatchingWithNoListeners()
    {
        $this->dispatcher->dispatch("foo", $this->event);
    }

    /**
     * Tests getting listeners
     */
    public function testGettingListeners()
    {
        $this->assertEquals([], $this->dispatcher->getListeners("foo"));
        $listener1 = [$this->listener, "doNothing1"];
        $listener2 = [$this->listener, "doNothing2"];
        $this->dispatcher->registerListener("foo", $listener1);
        $this->dispatcher->registerListener("foo", $listener2);
        $this->assertEquals([$listener1, $listener2], $this->dispatcher->getListeners("foo"));
    }

    /**
     * Test that a listener cannot be added twice
     */
    public function testListenerCannotBeAddedTwice()
    {
        $listener = [$this->listener, "doNothing1"];
        $this->dispatcher->registerListener("foo", $listener);
        $this->dispatcher->registerListener("foo", $listener);
        $this->assertEquals([$listener], $this->dispatcher->getListeners("foo"));
    }

    /**
     * Tests removing listeners
     */
    public function testRemovingListeners()
    {
        $listener1 = [$this->listener, "doNothing1"];
        $listener2 = [$this->listener, "doNothing2"];
        $this->dispatcher->registerListener("foo", $listener1);
        $this->dispatcher->registerListener("foo", $listener2);
        $this->dispatcher->removeListener("foo", $listener2);
        $this->assertEquals([$listener1], $this->dispatcher->getListeners("foo"));
        $this->assertTrue($this->dispatcher->hasListeners("foo"));
        $this->dispatcher->removeListener("foo", $listener1);
        $this->assertEquals([], $this->dispatcher->getListeners("foo"));
        $this->assertFalse($this->dispatcher->hasListeners("foo"));
    }

    /**
     * Tests that the second listener is not called when event propagation is stopped
     */
    public function testSecondListenerIsNotCalledWhenEventPropagationIsStopped()
    {
        $this->dispatcher->registerListener("foo", [$this->listener, "stopsPropagation"]);
        $this->dispatcher->registerListener("foo", [$this->listener, "doNothing1"]);
        // Need to manually stop the event from propagating in the stub
        $this->listener->expects($this->once())
            ->method("stopsPropagation")
            ->with($this->event, "foo", $this->dispatcher)
            ->willReturn($this->event->stopPropagation());
        $this->listener->expects($this->never())->method("doNothing1");
        $this->dispatcher->dispatch("foo", $this->event);
    }
}