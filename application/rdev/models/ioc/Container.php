<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an inversion of control container
 */
namespace RDev\Models\IoC;

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
            $this->bindUniversal($interface, $concreteClass);
        }
        else
        {
            $this->bindTargeted($interface, $concreteClass, $targetClass);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createNew($component, array $constructorPrimitives = [], array $methodCalls = [])
    {
        return $this->create($this->getConcreteClass($component), $constructorPrimitives, $methodCalls, true);
    }

    /**
     * {@inheritdoc}
     */
    public function createSingleton($component, array $constructorPrimitives = [], array $methodCalls = [])
    {
        $concreteClass = $this->getConcreteClass($component);

        if(($instance = $this->getInstance($concreteClass, $constructorPrimitives, $methodCalls)) !== null)
        {
            return $instance;
        }

        $instance = $this->create($this->getConcreteClass($component), $constructorPrimitives, $methodCalls, false);
        $this->registerInstance($instance, $constructorPrimitives, $methodCalls, false);

        return $instance;
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
    public function unbind($interface, $targetClass = null)
    {
        if($targetClass === null)
        {
            unset($this->universalBindings[$interface]);
        }
        elseif(isset($this->targetedBindings[$targetClass]))
        {
            unset($this->targetedBindings[$targetClass][$interface]);
        }
    }

    /**
     * Creates a component
     *
     * @param string $concreteClass The name of the concrete class to create
     * @param array $constructorPrimitives The primitive parameter values to pass into the constructor
     * @param array $methodCalls The array of method calls and their primitive parameter values
     *      Should be structured like so:
     *      [
     *          NAME_OF_METHOD => [VALUES_OF_PRIMITIVE_PARAMETERS],
     *          ...
     *      ]
     * @param bool $forceNewInstance True if we want a new instance, otherwise false and we'll share it
     * @return mixed The instantiated component
     * @throws IoCException Thrown if there was an error creating the instance
     */
    protected function create(
        $concreteClass,
        array $constructorPrimitives = [],
        array $methodCalls = [],
        $forceNewInstance = false
    )
    {
        // If we're creating a singleton, check to see if we've already instantiated it
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
                $forceNewInstance
            );
            $instance = $reflectionClass->newInstanceArgs($constructorParameters);
        }

        $this->callMethods($instance, $methodCalls, $forceNewInstance);

        return $instance;
    }

    /**
     * Creates a targeted binding
     *
     * @param string $interface The interface to bind to
     * @param string $concreteClass The concrete class to bind
     * @param string $targetClass The name of the target class to bind on
     */
    private function bindTargeted($interface, $concreteClass, $targetClass)
    {
        $this->targetedBindings[$targetClass][$interface] = $concreteClass;
    }

    /**
     * Creates a universal binding
     *
     * @param string $interface The interface to bind to
     * @param string $concreteClass The concrete class to bind
     */
    private function bindUniversal($interface, $concreteClass)
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
    private function callMethods(&$instance, array $methodCalls, $forceNewInstance)
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
    private function getConcreteClass($component)
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
    private function getInstance($concreteClass, array $constructorPrimitives = [], array $methodCalls = [])
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
     * @param bool $forceNewInstances True if the dependencies should be new instances, otherwise they'll be singletons
     * @return array The list of parameters with all the dependencies resolved
     * @throws IoCException Thrown if there was an error resolving the parameters
     */
    private function getResolvedParameters(
        $callingClass,
        array $unresolvedParameters,
        array $primitives,
        $forceNewInstances
    )
    {
        $resolvedParameters = [];

        foreach($unresolvedParameters as $parameter)
        {
            if($parameter->getClass() === null)
            {
                // The parameter is a primitive
                if(count($primitives) > 0)
                {
                    $resolvedParameters[] = array_shift($primitives);
                }
                elseif($parameter->isDefaultValueAvailable())
                {
                    // No value was found, so use the default value
                    $resolvedParameters[] = $parameter->getDefaultValue();
                }
                else
                {
                    throw new IoCException("No default value available for {$parameter->getName()}");
                }
            }
            else
            {
                // The parameter is an object
                $resolvedParameters[] = $this->resolveClass(
                    $callingClass,
                    $parameter->getClass()->getName(),
                    $forceNewInstances
                );
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
    private function getTargetedBinding($interface, $targetClass)
    {
        if(isset($this->targetedBindings[$targetClass]) && isset($this->targetedBindings[$targetClass][$interface]))
        {
            return $this->targetedBindings[$targetClass][$interface];
        }

        return $this->getUniversalBinding($interface);
    }

    /**
     * Gets a universal binding
     *
     * @param string $interface The name of the interface whose binding we want
     * @return string|null The name of the concrete class bound to the interface if it exists, otherwise null
     */
    private function getUniversalBinding($interface)
    {
        if(isset($this->universalBindings[$interface]))
        {
            return $this->universalBindings[$interface];
        }

        return null;
    }

    /**
     * Registers a new instance of a class
     *
     * @param mixed $instance The instance of a class
     * @param array $constructorPrimitives The list of constructor primitives used to create the instance
     * @param array $methodCalls The list of method names to their primitives used to create the instance
     */
    private function registerInstance($instance, array $constructorPrimitives = [], array $methodCalls = [])
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
    private function resolveClass($callingClass, $component, $forceNewInstance)
    {
        $concreteClass = $this->getBinding($component, $callingClass);

        if($concreteClass === null)
        {
            $concreteClass = $component;
        }

        if($forceNewInstance)
        {
            return $this->createNew($concreteClass);
        }
        else
        {
            return $this->createSingleton($concreteClass);
        }
    }
} 