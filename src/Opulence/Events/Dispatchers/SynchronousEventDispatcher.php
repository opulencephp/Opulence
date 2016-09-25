<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Events\Dispatchers;

use Opulence\Events\IEvent;

/**
 * Defines the synchronous event dispatcher
 */
class SynchronousEventDispatcher implements IEventDispatcher
{
    /** @var IEventRegistry The event registry */
    private $eventRegistry = null;

    /**
     * @param IEventRegistry $eventRegistry The event registry
     */
    public function __construct(IEventRegistry $eventRegistry)
    {
        $this->eventRegistry = $eventRegistry;
    }

    /**
     * @inheritdoc
     */
    public function dispatch(string $eventName, IEvent $event)
    {
        foreach ($this->eventRegistry->getListeners($eventName) as $listener) {
            $listener($event, $eventName, $this);

            if ($event->propagationIsStopped()) {
                break;
            }
        }
    }
}