<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Events\Tests\Dispatchers;

use Opulence\Events\Dispatchers\IEventRegistry;
use Opulence\Events\Dispatchers\SynchronousEventDispatcher;
use Opulence\Events\Tests\Mocks\Event;
use Opulence\Events\Tests\Mocks\Listener;

/**
 * Tests the event dispatcher
 */
class SynchronousEventDispatcherTest extends \PHPUnit\Framework\TestCase
{
    /** @var SynchronousEventDispatcher The dispatcher to use in tests */
    private $dispatcher = null;
    /** @var IEventRegistry|\PHPUnit_Framework_MockObject_MockObject The event registry to use in tests */
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
        $this->eventRegistry = $this->createMock(IEventRegistry::class);
        $this->dispatcher = new SynchronousEventDispatcher($this->eventRegistry);
        $this->event = new Event();
        $this->listener = $this->createMock(Listener::class);
    }

    /**
     * Tests dispatching to multiple listeners
     */
    public function testDispatchingToMultipleListeners()
    {
        $listeners = [
            [$this->listener, 'doNothing1'],
            [$this->listener, 'doNothing2']
        ];
        $this->eventRegistry->expects($this->once())
            ->method('getListeners')
            ->willReturn($listeners);
        $this->listener->expects($this->once())->method('doNothing1')->with($this->event, 'foo', $this->dispatcher);
        $this->listener->expects($this->once())->method('doNothing2')->with($this->event, 'foo', $this->dispatcher);
        $this->dispatcher->dispatch('foo', $this->event);
    }

    /**
     * Tests dispatching to a single listener
     */
    public function testDispatchingToSingleListener()
    {
        $listeners = [
            [$this->listener, 'doNothing1']
        ];
        $this->eventRegistry->expects($this->once())
            ->method('getListeners')
            ->willReturn($listeners);
        $this->listener->expects($this->once())->method('doNothing1')->with($this->event, 'foo', $this->dispatcher);
        $this->dispatcher->dispatch('foo', $this->event);
    }

    /**
     * Tests dispatching with no listeners
     */
    public function testDispatchingWithNoListeners()
    {
        $this->dispatcher->dispatch('foo', $this->event);
        // Essentially just test that we got here
        $this->assertTrue(true);
    }
}
