<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Bootstrappers\Events;

use InvalidArgumentException;
use Opulence\Bootstrappers\Bootstrapper;
use Opulence\Events\Dispatchers\Dispatcher;
use Opulence\Events\Dispatchers\IDispatcher;
use Opulence\Events\IEvent;
use Opulence\Ioc\IContainer;

/**
 * Defines the event dispatcher bootstrapper
 */
abstract class DispatcherBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $dispatcher = $this->getEventDispatcher($container);

        foreach ($this->getEventListenerConfig() as $eventName => $listeners) {
            foreach ($listeners as $listener) {
                $dispatcher->registerListener($eventName, $this->getEventListenerCallback($listener, $container));
            }
        }

        $container->bind(IDispatcher::class, $dispatcher);
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
     * @return IDispatcher The event dispatcher
     */
    protected function getEventDispatcher(IContainer $container) : IDispatcher
    {
        return new Dispatcher();
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

            $listenerConfigParts = explode("@", $listenerConfig);
            $listenerClass = $listenerConfigParts[0];
            $listenerMethod = $listenerConfigParts[1];

            return function (IEvent $event, $eventName, IDispatcher $dispatcher) use (
                $container,
                $listenerClass,
                $listenerMethod
            ) {
                $listenerObject = $container->makeShared($listenerClass);
                call_user_func_array([$listenerObject, $listenerMethod], [$event, $eventName, $dispatcher]);
            };
        }

        throw new InvalidArgumentException(
            "Listener config must be either callable or string formatted like \"className@methodName\""
        );
    }
}