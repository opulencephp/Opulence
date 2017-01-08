<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Events\Bootstrappers;

use InvalidArgumentException;
use Opulence\Events\Dispatchers\EventRegistry;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Events\Dispatchers\SynchronousEventDispatcher;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;

/**
 * Defines the event dispatcher bootstrapper
 */
abstract class EventDispatcherBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $container->bindInstance(IEventDispatcher::class, $this->getEventDispatcher($container));
    }

    /**
     * Gets the list of event names to the list of listeners, which can be callables or "className@method" strings
     *
     * @return array The event listener config
     */
    abstract protected function getEventListenerConfig() : array;

    /**
     * Gets the event dispatcher
     *
     * @param IContainer $container The IoC container
     * @return IEventDispatcher The event dispatcher
     */
    protected function getEventDispatcher(IContainer $container) : IEventDispatcher
    {
        $eventRegistry = new EventRegistry();

        foreach ($this->getEventListenerConfig() as $eventName => $listeners) {
            foreach ($listeners as $listener) {
                $eventRegistry->registerListener($eventName, $this->getEventListenerCallback($listener, $container));
            }
        }

        return new SynchronousEventDispatcher($eventRegistry);
    }

    /**
     * Gets a callback for an event listener from a config
     *
     * @param callable|string $listenerConfig The callable or "className@method" string
     * @param IContainer $container The IoC container
     * @return callable The event listener callable
     */
    protected function getEventListenerCallback($listenerConfig, IContainer $container) : callable
    {
        if (is_callable($listenerConfig)) {
            return $listenerConfig;
        }

        if (is_string($listenerConfig)) {
            if (strpos($listenerConfig, "@") === false) {
                throw new InvalidArgumentException("Listener data \"$listenerConfig\" is incorrectly formatted");
            }

            list($listenerClass, $listenerMethod) = explode("@", $listenerConfig);

            return function ($event, $eventName, IEventDispatcher $dispatcher) use (
                $container,
                $listenerClass,
                $listenerMethod
            ) {
                $listenerObject = $container->resolve($listenerClass);
                $listenerObject->$listenerMethod($event, $eventName, $dispatcher);
            };
        }

        throw new InvalidArgumentException(
            "Listener config must be either callable or string formatted like \"className@methodName\""
        );
    }
}
