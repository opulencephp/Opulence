<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Ioc;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;

/**
 * Defines an inversion of control container
 */
class Container implements IContainer
{
    /**
     * The list of class names to data about their instances
     * Formatted like:
     *      INTERFACE_NAME => [
     *          "instance" => INSTANCE,
     *          "constructorPrimitives" => [LIST_OF_CONSTRUCTOR_PRIMITIVES],
     *          "methodCalls" => [
     *              METHOD_NAME => [LIST_OF_METHOD_PRIMITIVES]
     *          ]
     *      ]
     *
     * @var array
     */
    protected $instances = [];
    /**
     * The list of target class names to interface => [
     *      "concrete" => Name of concrete class,
     *      "callback" => The optional callable to create the instance,
     *      "used" => Whether or not the binding has been used
     * ]
     *
     * @var array The list of target class names to interface => concrete class names
     */
    protected $targetedBindings = [];
    /**
     * The universal list of interface => [
     *      "concrete" => Name of concrete class,
     *      "callback" => The optional callable to create the instance,
     *      "used" => Whether or not the binding has been used
     * ]
     *
     * @var array
     */
    protected $universalBindings = [];

    public function __sleep()
    {
        return "";
    }

    /**
     * @inheritdoc
     */
    public function bind($interfaces, $concrete, $targetClass = null)
    {
        if (!is_string($concrete) && !is_callable($concrete)) {
            $this->registerInstance($concrete);
            $concrete = get_class($concrete);
        }

        if ($targetClass === null) {
            $this->bindUniversally((array)$interfaces, $concrete);
        } else {
            $this->bindToTarget((array)$interfaces, $concrete, $targetClass);
        }
    }

    /**
     * @inheritdoc
     */
    public function call($function, array $primitives = [], $ignoreMissing = false, $forceNewInstance = false)
    {
        // We have to check if the method exists in case the class implements a __call() magic method
        // __call() will force all calls to is_callable() to return true
        if (
            !is_callable($function) ||
            (is_array($function) && count($function) == 2 && !method_exists($function[0], $function[1]))
        ) {
            if (!$ignoreMissing) {
                throw new IocException("Cannot call function");
            }

            return null;
        }

        // Resolve all the method parameters
        if ($function instanceof Closure) {
            $className = null;
            $parameters = (new ReflectionFunction($function))->getParameters();
        } else {
            $className = get_class($function[0]);
            $parameters = (new ReflectionMethod($function[0], $function[1]))->getParameters();
        }

        $methodParameters = $this->getResolvedParameters(
            $className,
            $parameters,
            $primitives,
            $forceNewInstance
        );

        return call_user_func_array($function, $methodParameters);
    }

    /**
     * @inheritdoc
     */
    public function getBinding($interface, $targetClass = null)
    {
        if ($targetClass === null) {
            return $this->getUniversalBinding($interface);
        } else {
            return $this->getTargetedBinding($interface, $targetClass);
        }
    }

    /**
     * @inheritdoc
     */
    public function isBound($interface, $targetClass = null)
    {
        if ($targetClass === null) {
            return $this->isBoundUniversally($interface);
        } else {
            return $this->isBoundToTarget($interface, $targetClass);
        }
    }

    /**
     * @inheritdoc
     */
    public function make($component, $forceNewInstance, array $constructorPrimitives = [], array $methodCalls = [])
    {
        try {
            $concrete = $this->getConcrete($component);

            // If we're creating a shared instance, check to see if we've already instantiated it
            if (!$forceNewInstance) {
                $instance = $this->getInstance($concrete, $constructorPrimitives, $methodCalls);

                if ($instance !== null) {
                    return $instance;
                }
            }

            if ($this->usesCallback($component)) {
                if ($forceNewInstance || !$this->callbackWasUsed($component)) {
                    $instance = $this->makeCallback($component);
                }
            } else {
                $reflectionClass = new ReflectionClass($concrete);

                if (!$reflectionClass->isInstantiable()) {
                    throw new IocException("$concrete is not instantiable");
                }

                $constructor = $reflectionClass->getConstructor();

                if ($constructor === null) {
                    // No constructor, so instantiating is easy
                    $instance = new $concrete;
                } else {
                    // Resolve all of the constructor parameters
                    $constructorParameters = $this->getResolvedParameters(
                        $concrete,
                        $constructor->getParameters(),
                        $constructorPrimitives,
                        false
                    );
                    $instance = $reflectionClass->newInstanceArgs($constructorParameters);
                }
            }

            $this->callMethods($instance, $methodCalls, false);

            if (!$forceNewInstance) {
                // Register this instance for next time
                $this->registerInstance($instance, $constructorPrimitives, $methodCalls);
            }

            return $instance;
        } catch (ReflectionException $ex) {
            throw new IocException("Failed to make object", 0, $ex);
        }
    }

    /**
     * @inheritdoc
     */
    public function makeNew($component, array $constructorPrimitives = [], array $methodCalls = [])
    {
        return $this->make($component, true, $constructorPrimitives, $methodCalls);
    }

    /**
     * @inheritdoc
     */
    public function makeShared($component, array $constructorPrimitives = [], array $methodCalls = [])
    {
        return $this->make($component, false, $constructorPrimitives, $methodCalls);
    }

    /**
     * @inheritdoc
     */
    public function unbind($interface, $targetClass = null)
    {
        if ($targetClass === null) {
            $this->unbindUniversally($interface);
        } else {
            $this->unbindFromTarget($interface, $targetClass);
        }
    }

    /**
     * Creates a targeted binding
     *
     * @param array $interfaces The interfaces to bind to
     * @param string|callable $concrete The concrete class or callback to bind
     * @param string $targetClass The name of the target class to bind on
     */
    protected function bindToTarget(array $interfaces, $concrete, $targetClass)
    {
        $binding = [
            "concrete" => is_string($concrete) ? $concrete : "",
            "callback" => is_callable($concrete) ? $concrete : null,
            "used" => false
        ];

        foreach ($interfaces as $interface) {
            $this->targetedBindings[$targetClass][$interface] = $binding;
        }
    }

    /**
     * Creates a universal binding
     *
     * @param array $interfaces The interfaces to bind to
     * @param string|callable $concrete The concrete class or callback to bind
     */
    protected function bindUniversally(array $interfaces, $concrete)
    {
        $binding = [
            "concrete" => is_string($concrete) ? $concrete : "",
            "callback" => is_callable($concrete) ? $concrete : null,
            "used" => false
        ];

        foreach ($interfaces as $interface) {
            $this->universalBindings[$interface] = $binding;
        }
    }

    /**
     * Calls methods on an instance
     *
     * @param mixed $instance The instance to call methods on
     * @param array $methodCalls The list of methods to call
     * @param bool $forceNewInstance True if we want a new instance, otherwise false
     * @throws IocException Thrown if there was a problem calling the methods
     */
    protected function callMethods(&$instance, array $methodCalls, $forceNewInstance)
    {
        // Call any methods
        foreach ($methodCalls as $methodName => $methodPrimitives) {
            $this->call([$instance, $methodName], $methodPrimitives, false, $forceNewInstance);
        }
    }

    /**
     * Gets whether or not a callback was already used for a component
     *
     * @param string $component The component whose callback we're checking
     * @param string|null $targetClass The target class, if there was one
     * @return bool True if the callback was used, otherwise false
     */
    protected function callbackWasUsed($component, $targetClass = null)
    {
        if ($targetClass === null) {
            $bindingData =& $this->universalBindings[$component];
        } else {
            $bindingData =& $this->targetedBindings[$targetClass][$component];
        }

        return $bindingData["used"];
    }

    /**
     * Gets the name of the concrete class bound to an abstract class/interface
     *
     * @param string $component The name of the abstract class/interface whose concrete class we're looking for
     * @param string|null $targetClass The target class
     * @return string The name of the concrete class bound to the component
     *      If the input was a concrete class, then it's returned
     */
    protected function getConcrete($component, $targetClass = null)
    {
        if ($targetClass === null) {
            if (isset($this->universalBindings[$component])) {
                return $this->universalBindings[$component]["concrete"];
            } else {
                return $component;
            }
        } else {
            if (isset($this->targetedBindings[$targetClass][$component])) {
                return $this->targetedBindings[$targetClass][$component]["concrete"];
            } else {
                return $component;
            }
        }
    }

    /**
     * Attempts to get an already-instantiated input class
     *
     * @param string|callable $concrete The name of the concrete class or callable whose instance we want
     * @param array $constructorPrimitives The list of constructor primitives used to create the instance
     * @param array $methodCalls The list of method names to their primitives used to create the instance
     * @return mixed|null The instance if it exists, otherwise false
     */
    protected function getInstance($concrete, array $constructorPrimitives = [], array $methodCalls = [])
    {
        if (!is_string($concrete) || strlen($concrete) == 0) {
            return null;
        }

        if (isset($this->instances[$concrete]) &&
            $this->instances[$concrete]["constructorPrimitives"] == $constructorPrimitives &&
            $this->instances[$concrete]["methodCalls"] == $methodCalls
        ) {
            return $this->instances[$concrete]["instance"];
        }

        return null;
    }

    /**
     * Gets a list of parameters for a function call with all the dependencies resolved
     *
     * @param string $callingClass The name of the class whose parameters we're resolving
     * @param ReflectionParameter[] $unresolvedParameters The list of unresolved parameters
     * @param array $primitives The list of primitive values
     * @param bool $forceNewInstances True if the dependencies should be new instances, otherwise they'll be shared
     * @return array The list of parameters with all the dependencies resolved
     * @throws IocException Thrown if there was an error resolving the parameters
     */
    protected function getResolvedParameters(
        $callingClass,
        array $unresolvedParameters,
        array $primitives,
        $forceNewInstances
    ) {
        $resolvedParameters = [];

        foreach ($unresolvedParameters as $parameter) {
            $resolvedParameter = null;

            if ($parameter->getClass() === null) {
                // The parameter is a primitive
                $resolvedParameter = $this->resolvePrimitive($parameter, $primitives);
            } else {
                // The parameter is an object
                $resolvedParameter = $this->resolveClass(
                    $callingClass,
                    $parameter->getClass()->getName(),
                    $forceNewInstances
                );
            }

            // PHP forces a reference operator when passing parameters by reference via an array
            if ($parameter->isPassedByReference()) {
                $resolvedParameters[] = &$resolvedParameter;
            } else {
                $resolvedParameters[] = $resolvedParameter;
            }
        }

        return $resolvedParameters;
    }

    /**
     * Gets a targeted binding
     * If none is found, then the universal binding is returned
     *
     * @param string $interface The name of the interface whose binding we want
     * @param string $targetClass The name of the target class whose binding we want
     * @return string|callable|null The name of the concrete class or callable bound to the interface if it exists,
     *      otherwise null
     */
    protected function getTargetedBinding($interface, $targetClass)
    {
        if ($this->isBoundToTarget($interface, $targetClass)) {
            $bindingData = $this->targetedBindings[$targetClass][$interface];

            if (is_callable($bindingData["callback"])) {
                return $bindingData["callback"];
            }

            return $bindingData["concrete"];
        }

        // Fallback on the universal binding
        return $this->getUniversalBinding($interface);
    }

    /**
     * Gets a universal binding
     *
     * @param string $interface The name of the interface whose binding we want
     * @return string|callable|null The name of the concrete class or callable bound to the interface if it exists,
     *      otherwise null
     */
    protected function getUniversalBinding($interface)
    {
        if ($this->isBound($interface)) {
            $bindingData = $this->universalBindings[$interface];

            if (is_callable($bindingData["callback"])) {
                return $bindingData["callback"];
            }

            return $this->universalBindings[$interface]["concrete"];
        }

        return null;
    }

    /**
     * Gets whether or not an interface is bound to a target
     *
     * @param string $interface The name of the interface to check
     * @param string $targetClass The target class
     * @return bool True if the interface is bound to a target, otherwise false
     */
    protected function isBoundToTarget($interface, $targetClass)
    {
        return isset($this->targetedBindings[$targetClass]) && isset($this->targetedBindings[$targetClass][$interface]);
    }

    /**
     * Gets whether or not an interface is bound universally
     *
     * @param string $interface The name of the interface to check
     * @return bool True if the interface is bound universally, otherwise false
     */
    protected function isBoundUniversally($interface)
    {
        return isset($this->universalBindings[$interface]);
    }

    /**
     * Makes a callback that was bound to a component
     *
     * @param string $component The component whose callback we're creating
     * @param string|null $targetClass The target class, if there was one
     * @return mixed The result of the callback
     * @throws IocException Thrown if the callback could not be called
     */
    protected function makeCallback($component, $targetClass = null)
    {
        if ($targetClass === null) {
            $bindingData =& $this->universalBindings[$component];
        } else {
            // Fallback to universal bindings
            if (isset($this->targetedBindings[$targetClass]) && $this->targetedBindings[$targetClass][$component] !== null) {
                $bindingData =& $this->targetedBindings[$targetClass][$component];
            } else {
                $bindingData =& $this->universalBindings[$component];
            }
        }

        if (!is_callable($bindingData["callback"])) {
            throw new IocException("Callback is invalid for $component");
        }

        $instance = call_user_func($bindingData["callback"]);
        $bindingData["concrete"] = get_class($instance);
        $bindingData["used"] = true;

        return $instance;
    }

    /**
     * Registers a new instance of a class
     *
     * @param mixed $instance The instance of a class
     * @param array $constructorPrimitives The list of constructor primitives used to create the instance
     * @param array $methodCalls The list of method names to their primitives used to create the instance
     */
    protected function registerInstance($instance, array $constructorPrimitives = [], array $methodCalls = [])
    {
        $this->instances[get_class($instance)] = [
            "instance" => $instance,
            "constructorPrimitives" => $constructorPrimitives,
            "methodCalls" => $methodCalls
        ];
    }

    /**
     * Resolves a class
     *
     * @param string $callingClass The name of the class that is attempting to resolve this class
     * @param string $component The name of the class to resolve
     * @param bool $forceNewInstance True if we want to force a new instance, otherwise false
     * @return mixed The instantiated class
     * @throws IocException Thrown if there was a problem resolving the class
     */
    protected function resolveClass($callingClass, $component, $forceNewInstance)
    {
        $concrete = $this->getBinding($component, $callingClass);

        if (is_callable($concrete)) {
            // If we are not forcing new instances, try finding a registered instance
            if (!$forceNewInstance) {
                $instance = $this->getInstance($this->getConcrete($component));

                if ($instance !== null) {
                    return $instance;
                }
            }

            $instance = $this->makeCallback($component, $callingClass);

            // Register this for next time
            if (!$forceNewInstance) {
                $this->registerInstance($instance);
            }

            return $instance;
        } elseif ($concrete === null) {
            $concrete = $component;
        }

        if ($forceNewInstance) {
            return $this->makeNew($concrete);
        } else {
            return $this->makeShared($concrete);
        }
    }

    /**
     * Resolves a primitive parameter
     *
     * @param ReflectionParameter $parameter The primitive parameter to resolve
     * @param array $primitives The list of primitive values
     * @return mixed The resolved primitive
     * @throws IocException Thrown if there was a problem resolving the primitive
     */
    protected function resolvePrimitive(ReflectionParameter $parameter, array &$primitives)
    {
        if (count($primitives) > 0) {
            // Grab the next primitive
            return array_shift($primitives);
        }

        if ($parameter->isDefaultValueAvailable()) {
            // No value was found, so use the default value
            return $parameter->getDefaultValue();
        }

        throw new IocException(sprintf("No default value available for %s in %s::%s()",
            $parameter->getName(),
            $parameter->getDeclaringClass()->getName(),
            $parameter->getDeclaringFunction()->getName()
        ));
    }

    /**
     * Unbinds an interface from a target
     *
     * @param string $interface The name of the interface to unbind
     * @param string $targetClass The name of the target to unbind from
     */
    protected function unbindFromTarget($interface, $targetClass)
    {
        if (isset($this->targetedBindings[$targetClass])) {
            unset($this->targetedBindings[$targetClass][$interface]);
        }
    }

    /**
     * Unbinds an interface universally
     *
     * @param string $interface The name of the interface to unbind
     */
    protected function unbindUniversally($interface)
    {
        unset($this->universalBindings[$interface]);
    }

    /**
     * Gets whether or not a component uses a callback to resolve a binding
     *
     * @param string $component The name of the abstract class/interface whose concrete class we're looking for
     * @param string|null $targetClass The target class
     * @return bool True if the component uses a callback, otherwise false
     */
    protected function usesCallback($component, $targetClass = null)
    {
        if ($targetClass === null) {
            return isset($this->universalBindings[$component]) &&
            is_callable($this->universalBindings[$component]["callback"]);
        } else {
            return isset($this->targetedBindings[$targetClass][$component]) &&
            is_callable($this->targetedBindings[$targetClass][$component]["callback"]);
        }
    }
} 