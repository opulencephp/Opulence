<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Events\Tests\Dispatchers;

use Opulence\Events\Dispatchers\IEventRegistry;
use Opulence\Events\Dispatchers\SynchronousEventDispatcher;
use Opulence\Events\Tests\Mocks\Event;
use Opulence\Events\Tests\Mocks\Listener;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the event dispatcher
 */
class SynchronousEventDispatcherTest extends \PHPUnit\Framework\TestCase
{
    /** @var SynchronousEventDispatcher The dispatcher to use in tests */
    private $dispatcher;
    /** @var IEventRegistry|MockObject The event registry to use in tests */
    private $eventRegistry;
    /** @var Event The event to use in tests */
    private $event;
    /** @var Listener|MockObject The mock listener to use in tests */
    private $listener;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->eventRegistry = $this->createMock(IEventRegistry::class);
        $this->dispatcher = new SynchronousEventDispatcher($this->eventRegistry);
        $this->event = new Event();
        $this->listener = $this->createMock(Listener::class);
    }

    /**
     * Tests dispatching to multiple listeners
     */
    public function testDispatchingToMultipleListeners(): void
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
    public function testDispatchingToSingleListener(): void
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
    public function testDispatchingWithNoListeners(): void
    {
        $this->dispatcher->dispatch('foo', $this->event);
        // Essentially just test that we got here
        $this->assertTrue(true);
    }
}
