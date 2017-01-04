<?php
/**
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
use RuntimeException;

/**
 * Defines the dependency injection container
 */
class Container implements IContainer
{
    /** The value for an empty target */
    private static $emptyTarget = null;
    /** @var array The stack of targets */
    protected $targetStack = [];
    /** @var IBinding[][] The list of bindings */
    protected $bindings = [];

    /**
     * Prepares the container for serialization
     */
    public function __sleep()
    {
        return "";
    }

    /**
     * @inheritdoc
     */
    public function bindFactory($interfaces, callable $factory, bool $resolveAsSingleton = false)
    {
        foreach ((array)$interfaces as $interface) {
            $this->addBinding($interface, new FactoryBinding($factory, $resolveAsSingleton));
        }
    }

    /**
     * @inheritdoc
     */
    public function bindInstance($interfaces, $instance)
    {
        foreach ((array)$interfaces as $interface) {
            $this->addBinding($interface, new InstanceBinding($instance));
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
                throw new IocException("Cannot call method");
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
        $this->targetStack[] = $targetClass;
        $result = $callback($this);
        array_pop($this->targetStack);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function hasBinding(string $interface) : bool
    {
        $target = $this->getCurrentTarget();

        if ($target !== self::$emptyTarget && $this->hasTargetedBinding($interface, $target)) {
            return true;
        }

        return $this->hasTargetedBinding($interface, self::$emptyTarget);
    }

    /**
     * @inheritdoc
     */
    public function resolve(string $interface)
    {
        if (!$this->hasBinding($interface)) {
            // Try just resolving this directly
            return $this->resolveClass($interface);
        }

        $binding = $this->getBinding($interface);

        switch (get_class($binding)) {
            case InstanceBinding::class:
                return $binding->getInstance();
            case ClassBinding::class:
                $instance = $this->resolveClass(
                    $binding->getConcreteClass(),
                    $binding->getConstructorPrimitives()
                );
                break;
            case FactoryBinding::class:
                $instance = ($binding->getFactory())();
                break;
            default:
                throw new RuntimeException('Invalid binding type "' . get_class($binding) . '"');
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
        $target = $this->getCurrentTarget();

        foreach ((array)$interfaces as $interface) {
            unset($this->bindings[$target][$interface]);
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
        $target = $this->getCurrentTarget();

        if (!isset($this->bindings[$target])) {
            $this->bindings[$target] = [];
        }

        $this->bindings[$target][$interface] = $binding;
    }

    /**
     * Gets a binding for an interface
     *
     * @param string $interface The interface whose binding we want
     * @return IBinding|null The binding if one exists, otherwise null
     */
    protected function getBinding(string $interface)
    {
        $target = $this->getCurrentTarget();

        // If there's a targeted binding, use it
        if ($target !== self::$emptyTarget && isset($this->bindings[$target][$interface])) {
            return $this->bindings[$target][$interface];
        }

        // If there's a universal binding, use it
        if (isset($this->bindings[self::$emptyTarget][$interface])) {
            return $this->bindings[self::$emptyTarget][$interface];
        }

        return null;
    }

    /**
     * Gets the current target, if there is one
     *
     * @return string|null The current target if there is one, otherwise null
     */
    protected function getCurrentTarget()
    {
        return end($this->targetStack) ?: self::$emptyTarget;
    }

    /**
     * Gets whether or not a targeted binding exists
     *
     * @param string $interface
     * @param string $target
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
            $reflectionClass = new ReflectionClass($class);

            if (!$reflectionClass->isInstantiable()) {
                throw new IocException(
                    sprintf(
                        "%s is not instantiable%s",
                        $class,
                        $this->getCurrentTarget() === null ? "" : " (dependency of {$this->getCurrentTarget()})"
                    )
                );
            }

            $constructor = $reflectionClass->getConstructor();

            if ($constructor === null) {
                // No constructor, so instantiating is easy
                return new $class;
            }

            $constructorParameters = $this->resolveParameters(
                $class,
                $constructor->getParameters(),
                $primitives
            );

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
     * Resolves a primitive parameter
     *
     * @param ReflectionParameter $parameter  The primitive parameter to resolve
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
}
