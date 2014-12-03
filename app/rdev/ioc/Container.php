<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an inversion of control container
 */
namespace RDev\IoC;

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
    /** @var array The list of target class names to interface => concrete class names */
    protected $targetedBindings = [];
    /** @var array The universal list of interface => concrete class names */
    protected $universalBindings = [];

    /**
     * {@inheritdoc}
     */
    public function bind($interface, $concrete, $targetClass = null)
    {
        $concreteClass = $concrete;

        if(!is_string($concrete))
        {
            $concreteClass = get_class($concrete);
            $this->registerInstance($concrete);
        }

        if($targetClass === null)
        {
            $this->bindUniversally($interface, $concreteClass);
        }
        else
        {
            $this->bindToTarget($interface, $concreteClass, $targetClass);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBinding($interface, $targetClass = null)
    {
        if($targetClass === null)
        {
            return $this->getUniversalBinding($interface);
        }
        else
        {
            return $this->getTargetedBinding($interface, $targetClass);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isBound($interface, $targetClass = null)
    {
        if($targetClass === null)
        {
            return $this->isBoundUniversally($interface);
        }
        else
        {
            return $this->isBoundToTarget($interface, $targetClass);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function make($component, $forceNewInstance, array $constructorPrimitives = [], array $methodCalls = [])
    {
        $concreteClass = $this->getConcreteClass($component);

        // If we're creating a shared instance, check to see if we've already instantiated it
        if(!$forceNewInstance)
        {
            $instance = $this->getInstance($concreteClass, $constructorPrimitives, $methodCalls);

            if($instance !== null)
            {
                return $instance;
            }
        }

        $reflectionClass = new \ReflectionClass($concreteClass);

        if(!$reflectionClass->isInstantiable())
        {
            throw new IoCException("$concreteClass is not instantiable");
        }

        $constructor = $reflectionClass->getConstructor();

        if($constructor === null)
        {
            // No constructor, so instantiating is easy
            $instance = new $concreteClass;
        }
        else
        {
            // Resolve all of the constructor parameters
            $constructorParameters = $this->getResolvedParameters(
                $concreteClass,
                $constructor->getParameters(),
                $constructorPrimitives,
                false
            );
            $instance = $reflectionClass->newInstanceArgs($constructorParameters);
        }

        $this->callMethods($instance, $methodCalls, false);

        if(!$forceNewInstance)
        {
            // Register this instance for next time
            $this->registerInstance($instance, $constructorPrimitives, $methodCalls);
        }

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function makeNew($component, array $constructorPrimitives = [], array $methodCalls = [])
    {
        return $this->make($component, true, $constructorPrimitives, $methodCalls);
    }

    /**
     * {@inheritdoc}
     */
    public function makeShared($component, array $constructorPrimitives = [], array $methodCalls = [])
    {
        return $this->make($component, false, $constructorPrimitives, $methodCalls);
    }

    /**
     * {@inheritdoc}
     */
    public function unbind($interface, $targetClass = null)
    {
        if($targetClass === null)
        {
            $this->unbindUniversally($interface);
        }
        else
        {
            $this->unbindFromTarget($interface, $targetClass);
        }
    }

    /**
     * Creates a targeted binding
     *
     * @param string $interface The interface to bind to
     * @param string $concreteClass The concrete class to bind
     * @param string $targetClass The name of the target class to bind on
     */
    protected function bindToTarget($interface, $concreteClass, $targetClass)
    {
        $this->targetedBindings[$targetClass][$interface] = $concreteClass;
    }

    /**
     * Creates a universal binding
     *
     * @param string $interface The interface to bind to
     * @param string $concreteClass The concrete class to bind
     */
    protected function bindUniversally($interface, $concreteClass)
    {
        $this->universalBindings[$interface] = $concreteClass;
    }

    /**
     * Calls methods on an instance
     *
     * @param mixed $instance The instance to call methods on
     * @param array $methodCalls The list of methods to call
     * @param bool $forceNewInstance True if we want a new instance, otherwise false
     * @throws IoCException Thrown if there was a problem calling the methods
     */
    protected function callMethods(&$instance, array $methodCalls, $forceNewInstance)
    {
        // Call any methods
        foreach($methodCalls as $methodName => $methodPrimitives)
        {
            // Resolve all the method parameters
            $reflectionMethod = new \ReflectionMethod($instance, $methodName);
            $methodParameters = $this->getResolvedParameters(
                get_class($instance),
                $reflectionMethod->getParameters(),
                $methodPrimitives,
                $forceNewInstance
            );
            call_user_func_array([$instance, $methodName], $methodParameters);
        }
    }

    /**
     * Gets the name of the concrete class bound to an abstract class/interface
     *
     * @param string $component The name of the abstract class/interface whose concrete class we're looking for
     * @return string The name of the concrete class bound to the component
     *      If the input was a concrete class, then it's returned
     */
    protected function getConcreteClass($component)
    {
        return isset($this->universalBindings[$component]) ? $this->universalBindings[$component] : $component;
    }

    /**
     * Attempts to get an already-instantiated input class
     *
     * @param string $concreteClass The name of the concrete class whose instance we want
     * @param array $constructorPrimitives The list of constructor primitives used to create the instance
     * @param array $methodCalls The list of method names to their primitives used to create the instance
     * @return mixed|null The instance if it exists, otherwise false
     */
    protected function getInstance($concreteClass, array $constructorPrimitives = [], array $methodCalls = [])
    {
        if(isset($this->instances[$concreteClass]) &&
            $this->instances[$concreteClass]["constructorPrimitives"] == $constructorPrimitives &&
            $this->instances[$concreteClass]["methodCalls"] == $methodCalls
        )
        {
            return $this->instances[$concreteClass]["instance"];
        }

        return null;
    }

    /**
     * Gets a list of parameters for a function call with all the dependencies resolved
     *
     * @param string $callingClass The name of the class whose parameters we're resolving
     * @param \ReflectionParameter[] $unresolvedParameters The list of unresolved parameters
     * @param array $primitives The list of primitive values
     * @param bool $forceNewInstances True if the dependencies should be new instances, otherwise they'll be shared
     * @return array The list of parameters with all the dependencies resolved
     * @throws IoCException Thrown if there was an error resolving the parameters
     */
    protected function getResolvedParameters(
        $callingClass,
        array $unresolvedParameters,
        array $primitives,
        $forceNewInstances
    )
    {
        $resolvedParameters = [];

        foreach($unresolvedParameters as $parameter)
        {
            $resolvedParameter = null;

            if($parameter->getClass() === null)
            {
                // The parameter is a primitive
                $resolvedParameter = $this->resolvePrimitive($parameter, $primitives);
            }
            else
            {
                // The parameter is an object
                $resolvedParameter = $this->resolveClass(
                    $callingClass,
                    $parameter->getClass()->getName(),
                    $forceNewInstances
                );
            }

            // PHP forces a reference operator when passing parameters by reference via an array
            if($parameter->isPassedByReference())
            {
                $resolvedParameters[] = &$resolvedParameter;
            }
            else
            {
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
     * @return string|null The name of the concrete class bound to the interface if it exists, otherwise null
     */
    protected function getTargetedBinding($interface, $targetClass)
    {
        if($this->isBoundToTarget($interface, $targetClass))
        {
            return $this->targetedBindings[$targetClass][$interface];
        }

        // Fallback on the universal binding
        return $this->getUniversalBinding($interface);
    }

    /**
     * Gets a universal binding
     *
     * @param string $interface The name of the interface whose binding we want
     * @return string|null The name of the concrete class bound to the interface if it exists, otherwise null
     */
    protected function getUniversalBinding($interface)
    {
        if($this->isBound($interface))
        {
            return $this->universalBindings[$interface];
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
     * @throws IoCException Thrown if there was a problem resolving the class
     */
    protected function resolveClass($callingClass, $component, $forceNewInstance)
    {
        $concreteClass = $this->getBinding($component, $callingClass);

        if($concreteClass === null)
        {
            $concreteClass = $component;
        }

        if($forceNewInstance)
        {
            return $this->makeNew($concreteClass);
        }
        else
        {
            return $this->makeShared($concreteClass);
        }
    }

    /**
     * Resolves a primitive parameter
     *
     * @param \ReflectionParameter $parameter The primitive parameter to resolve
     * @param array $primitives The list of primitive values
     * @return mixed The resolved primitive
     * @throws IoCException Thrown if there was a problem resolving the primitive
     */
    protected function resolvePrimitive(\ReflectionParameter $parameter, array &$primitives)
    {
        if(count($primitives) > 0)
        {
            // Grab the next primitive
            return array_shift($primitives);
        }

        if($parameter->isDefaultValueAvailable())
        {
            // No value was found, so use the default value
            return $parameter->getDefaultValue();
        }

        throw new IoCException(sprintf("No default value available for %s in %s::%s()",
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
        if(isset($this->targetedBindings[$targetClass]))
        {
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
} 