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
    const EMPTY_TARGET = null;

    /** @var array The stack of targets */
    protected $targetStack = [];

    /** @var array */
    protected $definitions = [];
    /** @var array */
    protected $instances = [];

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
            $this->addDefinition($interface, new FactoryBinding($factory, $resolveAsSingleton));
        }
    }

    /**
     * @inheritdoc
     */
    public function bindInstance($interfaces, $instance)
    {
        foreach ((array)$interfaces as $interface) {
            $this->addInstance($interface, $instance);
        }
    }

    /**
     * @inheritdoc
     */
    public function bindPrototype($interfaces, string $concreteClass = null, array $primitives = [])
    {
        foreach ((array)$interfaces as $interface) {
            $this->addDefinition($interface, new ClassBinding($concreteClass ?? $interface, $primitives, false));
        }
    }

    /**
     * @inheritdoc
     */
    public function bindSingleton($interfaces, string $concreteClass = null, array $primitives = [])
    {
        foreach ((array)$interfaces as $interface) {
            $this->addDefinition($interface, new ClassBinding($concreteClass ?? $interface, $primitives, true));
        }
    }

    /**
     * @inheritdoc
     */
    public function callClosure(callable $closure, array $primitives = [])
    {
        $unresolvedParameters = (new ReflectionFunction($closure))->getParameters();
        $resolvedParameters   = $this->resolveParameters(null, $unresolvedParameters, $primitives);

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
        $className            = is_string($instance) ? $instance : get_class($instance);
        $resolvedParameters   = $this->resolveParameters($className, $unresolvedParameters, $primitives);

        return ([$instance, $methodName])(...$resolvedParameters);
    }

    /**
     * @inheritdoc
     */
    public function for (string $targetClass, callable $callback)
    {
        $this->targetStack[] = $targetClass;
        $result              = $callback($this);
        array_pop($this->targetStack);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function hasBinding(string $interface): bool
    {
        $target = $this->getCurrentTarget();

        if ($target !== self::EMPTY_TARGET && $this->hasTargetedBinding($interface, $target)) {
            return true;
        }

        return $this->hasTargetedBinding($interface, self::EMPTY_TARGET);
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

        $instance = $this->getInstance($interface);
        if ($instance !== null) {
            return $instance;
        }

        $definition = $this->getDefinition($interface);
        if ($definition instanceof ClassBinding) {
            $instance = $this->resolveClass(
                $definition->getConcreteClass(),
                $definition->getConstructorPrimitives()
            );
        } elseif ($definition instanceof FactoryBinding) {
            $instance = $this->callClosure($definition->getFactory());
        } else {
            throw new RuntimeException('Invalid binding type "' . get_class($definition) . '');
        }

        if ($definition->resolveAsSingleton()) {
            $this->unbind($interface);
            $this->addInstance($interface, $instance);
        }

        return $instance;
    }

    /**
     * @inheritdoc
     */
    public function unbind($interfaces)
    {
        foreach ((array)$interfaces as $interface) {
            $target = $this->getCurrentTarget();

            unset($this->instances[$target][$interface]);
            unset($this->definitions[$target][$interface]);
        }
    }

    protected function addDefinition(string $interface, $binding)
    {
        $this->addBinding($interface, $binding, $this->definitions);
    }

    protected function addInstance(string $interface, $instance)
    {
        $this->addBinding($interface, $instance, $this->instances);
    }

    protected function addBinding($interface, $instance, &$collection)
    {
        $target = $this->getCurrentTarget();

        if (!isset($collection[$target])) {
            $collection[$target] = [];
        }

        $collection[$target][$interface] = $instance;
    }

    /**
     * Gets the current target, if there is one
     *
     * @return string|null The current target if there is one, otherwise null
     */
    protected function getCurrentTarget()
    {
        return end($this->targetStack) ?: self::EMPTY_TARGET;
    }

    /**
     * @param string $interface
     * @return IBinding|null
     */
    protected function getDefinition(string $interface)
    {
        return $this->getBinding($interface, $this->definitions);
    }

    /**
     * @param string $interface
     * @return IBinding|null
     */
    protected function getInstance(string $interface)
    {
        return $this->getBinding($interface, $this->instances);
    }

    /**
     * @param string $interface
     * @param array  $collection
     * @return IBinding|null
     */
    protected function getBinding(string $interface, &$collection)
    {
        $target = $this->getCurrentTarget();

        if ($target !== self::EMPTY_TARGET && isset($collection[$target][$interface])) {
            return $collection[$target][$interface];
        }

        if (isset($collection[self::EMPTY_TARGET][$interface])) {
            return $collection[self::EMPTY_TARGET][$interface];
        }

        return null;
    }

    /**
     * Resolves a class
     *
     * @param string $class      The class name to resolve
     * @param array  $primitives The list of constructor primitives
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
     * @param string|null           $class                The name of the class whose parameters we're resolving
     * @param ReflectionParameter[] $unresolvedParameters The list of unresolved parameters
     * @param array                 $primitives           The list of primitive values
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

    protected function hasTargetedBinding(string $interface, string $target = null)
    {
        return isset($this->instances[$target][$interface])
            || isset($this->definitions[$target][$interface]);
    }

    /**
     * Resolves a primitive parameter
     *
     * @param ReflectionParameter $parameter  The primitive parameter to resolve
     * @param array               $primitives The list of primitive values
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
