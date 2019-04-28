<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Events\Dispatchers;

/**
 * Defines the synchronous event dispatcher
 */
class SynchronousEventDispatcher implements IEventDispatcher
{
    /** @var IEventRegistry The event registry */
    private $eventRegistry;

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
    public function dispatch(string $eventName, $event): void
    {
        foreach ($this->eventRegistry->getListeners($eventName) as $listener) {
            $listener($event, $eventName, $this);
        }
    }
}
