<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc;

use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;

/**
 * Defines the dependency injection container
 */
class Container implements IContainer
{
    /** The value for an empty target */
    private static $emptyTarget = null;
    /** @var null|string The current target */
    protected $currentTarget = null;
    /** @var array The stack of targets */
    protected $targetStack = [];
    /** @var IBinding[][] The list of bindings */
    protected $bindings = [];
    /** @var array The cache of reflection constructors and their parameters */
    protected $constructorReflectionCache = [];

    /**
     * Prepares the container for serialization
     */
    public function __sleep()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function bindFactory($interfaces, callable $factory, bool $resolveAsSingleton = false)
    {
        $binding = new FactoryBinding($factory, $resolveAsSingleton);

        foreach ((array)$interfaces as $interface) {
            $this->addBinding($interface, $binding);
        }
    }

    /**
     * @inheritdoc
     */
    public function bindInstance($interfaces, $instance)
    {
        $binding = new InstanceBinding($instance);

        foreach ((array)$interfaces as $interface) {
            $this->addBinding($interface, $binding);
        }
    }

    /**
     * @inheritdoc
     */
    public function bindPrototype($interfaces, string $concreteClass = null, array $primitives = [])
    {
        foreach ((array)$interfaces as $interface) {
            $this->addBinding($interface, new ClassBinding($concreteClass ?? $interface, $primitives, false));
        }
    }

    /**
     * @inheritdoc
     */
    public function bindSingleton($interfaces, string $concreteClass = null, array $primitives = [])
    {
        foreach ((array)$interfaces as $interface) {
            $this->addBinding($interface, new ClassBinding($concreteClass ?? $interface, $primitives, true));
        }
    }

    /**
     * @inheritdoc
     */
    public function callClosure(callable $closure, array $primitives = [])
    {
        $unresolvedParameters = (new ReflectionFunction($closure))->getParameters();
        $resolvedParameters = $this->resolveParameters(null, $unresolvedParameters, $primitives);

        return $closure(...$resolvedParameters);
    }

    /**
     * @inheritdoc
     */
    public function callMethod($instance, string $methodName, array $primitives = [], bool $ignoreMissingMethod = false)
    {
        if (!method_exists($instance, $methodName)) {
            if (!$ignoreMissingMethod) {
                throw new IocException('Cannot call method');
            }

            return null;
        }

        $unresolvedParameters = (new ReflectionMethod($instance, $methodName))->getParameters();
        $className = is_string($instance) ? $instance : get_class($instance);
        $resolvedParameters = $this->resolveParameters($className, $unresolvedParameters, $primitives);

        return ([$instance, $methodName])(...$resolvedParameters);
    }

    /**
     * @inheritdoc
     */
    public function for (string $targetClass, callable $callback)
    {
        $this->currentTarget = $targetClass;
        $this->targetStack[] = $targetClass;

        $result = $callback($this);

        array_pop($this->targetStack);
        $this->currentTarget = end($this->targetStack) ?: self::$emptyTarget;

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function hasBinding(string $interface) : bool
    {
        if ($this->currentTarget !== self::$emptyTarget
            && $this->hasTargetedBinding($interface, $this->currentTarget)
        ) {
            return true;
        }

        return $this->hasTargetedBinding($interface, self::$emptyTarget);
    }

    /**
     * @inheritdoc
     */
    public function resolve(string $interface)
    {
        $binding = $this->getBinding($interface);

        if ($binding === null) {
            // Try just resolving this directly
            return $this->resolveClass($interface);
        }

        switch (get_class($binding)) {
            case InstanceBinding::class:
                /** @var InstanceBinding $binding */
                return $binding->getInstance();
            case ClassBinding::class:
                /** @var ClassBinding $binding */
                $instance = $this->resolveClass(
                    $binding->getConcreteClass(),
                    $binding->getConstructorPrimitives()
                );
                break;
            case FactoryBinding::class:
                /** @var FactoryBinding $binding */
                $factory = $binding->getFactory();
                $instance = $factory();
                break;
            default:
                throw new IoCException('Invalid binding type "' . get_class($binding) . '"');
        }

        if ($binding->resolveAsSingleton()) {
            $this->unbind($interface);
            $this->addBinding($interface, new InstanceBinding($instance));
        }

        return $instance;
    }

    /**
     * @inheritdoc
     */
    public function unbind($interfaces)
    {
        foreach ((array)$interfaces as $interface) {
            unset($this->bindings[$this->currentTarget][$interface]);
        }
    }

    /**
     * Adds a binding to an interface
     *
     * @param string $interface The interface to bind to
     * @param IBinding $binding The binding to add
     */
    protected function addBinding(string $interface, IBinding $binding)
    {
        if (!isset($this->bindings[$this->currentTarget])) {
            $this->bindings[$this->currentTarget] = [];
        }

        $this->bindings[$this->currentTarget][$interface] = $binding;
    }

    /**
     * Gets a binding for an interface
     *
     * @param string $interface The interface whose binding we want
     * @return IBinding|null The binding if one exists, otherwise null
     */
    protected function getBinding(string $interface)
    {
        // If there's a targeted binding, use it
        if ($this->currentTarget !== self::$emptyTarget && isset($this->bindings[$this->currentTarget][$interface])) {
            return $this->bindings[$this->currentTarget][$interface];
        }

        // If there's a universal binding, use it
        if (isset($this->bindings[self::$emptyTarget][$interface])) {
            return $this->bindings[self::$emptyTarget][$interface];
        }

        return null;
    }

    /**
     * Gets whether or not a targeted binding exists
     *
     * @param string $interface The interface to check
     * @param string|null $target The target whose bindings we're checking
     * @return bool True if the targeted binding exists, otherwise false
     */
    protected function hasTargetedBinding(string $interface, string $target = null) : bool
    {
        return isset($this->bindings[$target][$interface]);
    }

    /**
     * Resolves a class
     *
     * @param string $class The class name to resolve
     * @param array $primitives The list of constructor primitives
     * @return object The resolved class
     * @throws IocException Thrown if the class could not be resolved
     */
    protected function resolveClass(string $class, array $primitives = [])
    {
        try {
            if (isset($this->constructorReflectionCache[$class])) {
                list($constructor, $parameters) = $this->constructorReflectionCache[$class];
            } else {
                $reflectionClass = new ReflectionClass($class);
                if (!$reflectionClass->isInstantiable()) {
                    throw new IocException(
                        sprintf(
                            '%s is not instantiable%s',
                            $class,
                            $this->currentTarget === null ? '' : " (dependency of {$this->currentTarget})"
                        )
                    );
                }

                $constructor = $reflectionClass->getConstructor();
                $parameters = $constructor !== null ? $constructor->getParameters() : null;
                $this->constructorReflectionCache[$class] = [$constructor, $parameters];
            }

            if ($constructor === null) {
                // No constructor, so instantiating is easy
                return new $class;
            }

            $constructorParameters = $this->resolveParameters($class, $parameters, $primitives);

            return new $class(...$constructorParameters);
        } catch (ReflectionException $ex) {
            throw new IocException("Failed to resolve class $class", 0, $ex);
        }
    }

    /**
     * Resolves a list of parameters for a function call
     *
     * @param string|null $class The name of the class whose parameters we're resolving
     * @param ReflectionParameter[] $unresolvedParameters The list of unresolved parameters
     * @param array $primitives The list of primitive values
     * @return array The list of parameters with all the dependencies resolved
     * @throws IocException Thrown if there was an error resolving the parameters
     */
    protected function resolveParameters(
        $class,
        array $unresolvedParameters,
        array $primitives
    ) : array {
        $resolvedParameters = [];

        foreach ($unresolvedParameters as $parameter) {
            $resolvedParameter = null;

            if ($parameter->getClass() === null) {
                // The parameter is a primitive
                $resolvedParameter = $this->resolvePrimitive($parameter, $primitives);
            } else {
                // The parameter is an object
                $parameterClassName = $parameter->getClass()->getName();

                /**
                 * We need to first check if the input class is a target for the parameter
                 * If it is, resolve it using the input class as a target
                 * Otherwise, attempt to resolve it universally
                 */
                if ($class !== null && $this->hasTargetedBinding($parameterClassName, $class)) {
                    $resolvedParameter = $this->for($class, function (IContainer $container) use ($parameter) {
                        return $container->resolve($parameter->getClass()->getName());
                    });
                } else {
                    $resolvedParameter = $this->resolve($parameterClassName);
                }
            }

            $resolvedParameters[] = $resolvedParameter;
        }

        return $resolvedParameters;
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

        throw new IocException(sprintf('No default value available for %s in %s::%s()',
            $parameter->getName(),
            $parameter->getDeclaringClass()->getName(),
            $parameter->getDeclaringFunction()->getName()
        ));
    }
}
