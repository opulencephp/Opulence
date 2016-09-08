<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
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
    /** @var array The stack of targets */
    protected $targetStack = [];
    /** @var array The mapping of interfaces to factory bindings */
    protected $universalFactories = [];
    /** @var array The mapping of interfaces to an array of instances */
    protected $universalInstances = [];
    /** @var array The mapping of interfaces to an array of prototype class bindings */
    protected $universalPrototypes = [];
    /** @var array The mapping of interfaces to an array of singleton class bindings */
    protected $universalSingletons = [];
    /** @var array The mapping of targets to interfaces to factory bindings */
    protected $targetedFactories = [];
    /** @var array The mapping of targets to interfaces to an array of instances */
    protected $targetedInstances = [];
    /** @var array The mapping of targets to interfaces to an array of prototypes and constructor primitives */
    protected $targetedPrototypes = [];
    /** @var array The mapping of targets to interfaces to an array of singletons and constructor primitives */
    protected $targetedSingletons = [];
    /** @var array The mapping of universal bindings to their binding types */
    private $universalBindingTypeMappings = [];
    /** @var array The mapping of targeted bindings to their binding types */
    private $targetedBindingTypeMappings = [];

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
            $this->doFactoryBinding($interface, $factory, $resolveAsSingleton);
        }
    }

    /**
     * @inheritdoc
     */
    public function bindInstance($interfaces, $instance)
    {
        foreach ((array)$interfaces as $interface) {
            $this->doInstanceBinding($interface, $instance);
        }
    }

    /**
     * @inheritdoc
     */
    public function bindPrototype($interfaces, string $concreteClass = null, array $primitives = [])
    {
        foreach ((array)$interfaces as $interface) {
            $this->doPrototypeBinding($interface, $concreteClass, $primitives);
        }
    }

    /**
     * @inheritdoc
     */
    public function bindSingleton($interfaces, string $concreteClass = null, array $primitives = [])
    {
        foreach ((array)$interfaces as $interface) {
            $this->doSingletonBinding($interface, $concreteClass, $primitives);
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

        return [$instance, $methodName](...$resolvedParameters);
    }

    /**
     * @inheritdoc
     */
    public function for (string $targetClass, callable $callback)
    {
        $this->targetStack[] = $targetClass;
        $result = $callback($this);
        $this->stopTargeting();

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function hasBinding(string $interface) : bool
    {
        $hasBinding = $this->getBindingData($interface, false) !== null;

        return $hasBinding;
    }

    /**
     * @inheritdoc
     */
    public function resolve(string $interface)
    {
        $bindingData = $this->getBindingData($interface, true);

        if ($bindingData === null) {
            // Try just resolving this directly
            return $this->resolveClass($interface);
        }

        $bindingType = $bindingData[0];
        $target = $bindingData[1];

        switch ($bindingType) {
            case "f":
                $factoryBinding = $this->getFactory($interface, $target);
                $instance = $this->callClosure($factoryBinding->getFactory());

                // If we are to resolve as a singleton, then remove the factory binding
                if ($factoryBinding->resolveAsSingleton()) {
                    $this->unbind($interface);
                    $this->doInstanceBinding($interface, $instance);
                }

                return $instance;
            case "i":
                return $this->getInstance($interface, $target);
            case "p":
                $classBinding = $this->getPrototype($interface, $target);

                return $this->resolveClass(
                    $classBinding->getConcreteClass(),
                    $classBinding->getConstructorPrimitives()
                );
            case "s":
                $classBinding = $this->getSingleton($interface, $target);
                $instance = $this->resolveClass(
                    $classBinding->getConcreteClass(),
                    $classBinding->getConstructorPrimitives()
                );

                $this->doInstanceBinding($interface, $instance);

                return $instance;
            default:
                throw new RuntimeException("Invalid binding type \"$bindingType\"");
        }
    }

    /**
     * @inheritdoc
     */
    public function unbind($interfaces)
    {
        foreach ((array)$interfaces as $interface) {
            $bindingData = $this->getBindingData($interface, true);

            if ($bindingData === null) {
                continue;
            }

            $bindingType = $bindingData[0];
            $target = $bindingData[1];

            if ($target === null) {
                switch ($bindingType) {
                    case "f":
                        unset($this->universalFactories[$interface]);
                        break;
                    case "i":
                        unset($this->universalInstances[$interface]);
                        break;
                    case "p":
                        unset($this->universalPrototypes[$interface]);
                        break;
                    case "s":
                        unset($this->universalSingletons[$interface]);
                        break;
                }

                unset($this->universalBindingTypeMappings[$interface]);
            } else {
                switch ($bindingType) {
                    case "f":
                        if (isset($this->targetedFactories[$target])) {
                            unset($this->targetedFactories[$target][$interface]);
                        }

                        break;
                    case "i":
                        if (isset($this->targetedInstances[$target])) {
                            unset($this->targetedInstances[$target][$interface]);
                        }

                        break;
                    case "p":
                        if (isset($this->targetedPrototypes[$target])) {
                            unset($this->targetedPrototypes[$target][$interface]);
                        }

                        break;
                    case "s":
                        if (isset($this->targetedSingletons[$target])) {
                            unset($this->targetedSingletons[$target][$interface]);
                        }

                        break;
                }

                if (isset($this->targetedBindingTypeMappings[$target])) {
                    unset($this->targetedBindingTypeMappings[$target][$interface]);
                }
            }
        }
    }

    /**
     * Does the factory binding
     *
     * @param string $interface The interface to bind to
     * @param callable $factory The factory to bind
     * @param bool $resolveAsSingleton Whether or not to resolve the factory as a singleton
     */
    protected function doFactoryBinding(string $interface, callable $factory, bool $resolveAsSingleton = false)
    {
        $binding = new FactoryBinding($factory, $resolveAsSingleton);

        if ($this->usingTarget()) {
            $target = $this->getCurrentTarget();

            if (!isset($this->targetedFactories[$target])) {
                $this->targetedFactories[$target] = [];
            }

            $this->targetedFactories[$target][$interface] = $binding;
            $this->mapTargetedBindingType($interface, $target, "f");
        } else {
            $this->universalFactories[$interface] = $binding;
            $this->mapUniversalBindingType($interface, "f");
        }
    }

    /**
     * Does the binding of an instance to the interface
     *
     * @param string $interface The interface to bind to
     * @param object $instance The instance to bind
     */
    protected function doInstanceBinding(string $interface, $instance)
    {
        if ($this->usingTarget()) {
            $target = $this->getCurrentTarget();

            if (!isset($this->targetedInstances[$target])) {
                $this->targetedInstances[$target] = [];
            }

            $this->targetedInstances[$target][$interface] = $instance;
            $this->mapTargetedBindingType($interface, $target, "i");
        } else {
            $this->universalInstances[$interface] = $instance;
            $this->mapUniversalBindingType($interface, "i");
        }
    }

    /**
     * Does the binding of a non-singleton to the interface
     *
     * @param string $interface The interface to bind to
     * @param string|null $concreteClass The concrete class to bind, or null if the interface actually is a concrete class
     * @param array $primitives The list of primitives to inject (must be in same order they appear in constructor)
     */
    protected function doPrototypeBinding(string $interface, string $concreteClass = null, array $primitives = [])
    {
        $binding = new ClassBinding($concreteClass ?? $interface, $primitives);

        if ($this->usingTarget()) {
            $target = $this->getCurrentTarget();

            if (!isset($this->targetedPrototypes[$target])) {
                $this->targetedPrototypes[$target] = [];
            }

            $this->targetedPrototypes[$target][$interface] = $binding;
            $this->mapTargetedBindingType($interface, $target, "p");
        } else {
            $this->universalPrototypes[$interface] = $binding;
            $this->mapUniversalBindingType($interface, "p");
        }
    }

    /**
     * Does the binding of a singleton to the interface
     *
     * @param string $interface The interface to bind to
     * @param string|null $concreteClass The concrete class to bind, or null if the interface actually is a concrete class
     * @param array $primitives The list of primitives to inject (must be in same order they appear in constructor)
     */
    protected function doSingletonBinding(string $interface, string $concreteClass = null, array $primitives = [])
    {
        $binding = new ClassBinding($concreteClass ?? $interface, $primitives);

        if ($this->usingTarget()) {
            $target = $this->getCurrentTarget();

            if (!isset($this->targetedSingletons[$target])) {
                $this->targetedSingletons[$target] = [];
            }

            $this->targetedSingletons[$target][$interface] = $binding;
            $this->mapTargetedBindingType($interface, $target, "s");
        } else {
            $this->universalSingletons[$interface] = $binding;
            $this->mapUniversalBindingType($interface, "s");
        }
    }

    /**
     * Gets the current target, if there is one
     *
     * @return string|null The current target if there is one, otherwise null
     */
    protected function getCurrentTarget()
    {
        if (count($this->targetStack) > 0) {
            return $this->targetStack[count($this->targetStack) - 1];
        }

        return null;
    }

    /**
     * Gets the factory for an interface
     *
     * @param string $interface The interface to check
     * @param string|null $target The target if we're checking for a targeted binding, otherwise null
     * @return FactoryBinding|null The factory binding if there was one, otherwise null
     */
    protected function getFactory(string $interface, string $target = null)
    {
        // Fallback to universal bindings if there is no targeted binding
        if ($target === null || !isset($this->targetedFactories[$target])) {
            if (!isset($this->universalFactories[$interface])) {
                return null;
            }

            return $this->universalFactories[$interface];
        }

        if (!isset($this->targetedFactories[$target][$interface])) {
            return null;
        }

        return $this->targetedFactories[$target][$interface];
    }

    /**
     * Gets the instance for an interface
     *
     * @param string $interface The interface to check
     * @param string|null $target The target if we're checking for a targeted binding, otherwise null
     * @return mixed|null The instance if there was one, otherwise null
     */
    protected function getInstance(string $interface, string $target = null)
    {
        // Fallback to universal bindings if there is no targeted binding
        if ($target === null || !isset($this->targetedInstances[$target])) {
            if (!isset($this->universalInstances[$interface])) {
                return null;
            }

            return $this->universalInstances[$interface];
        }

        if (!isset($this->targetedInstances[$target][$interface])) {
            return null;
        }

        return $this->targetedInstances[$target][$interface];
    }

    /**
     * Gets the prototype for an interface
     *
     * @param string $interface The interface to check
     * @param string|null $target The target if we're checking for a targeted binding, otherwise null
     * @return ClassBinding|null The class binding if there was one, otherwise null
     */
    protected function getPrototype(string $interface, string $target = null)
    {
        // Fallback to universal bindings if there is no targeted binding
        if ($target === null || !isset($this->targetedPrototypes[$target])) {
            if (!isset($this->universalPrototypes[$interface])) {
                return null;
            }

            return $this->universalPrototypes[$interface];
        }

        if (!isset($this->targetedPrototypes[$target][$interface])) {
            return null;
        }

        return $this->targetedPrototypes[$target][$interface];
    }

    /**
     * Gets the targeted singleton for an interface
     *
     * @param string $interface The interface to check
     * @param string|null $target The target if we're checking for a targeted binding, otherwise null
     * @return ClassBinding|null The class binding if there was one, otherwise null
     */
    protected function getSingleton(string $interface, string $target = null)
    {
        // Fallback to universal bindings if there is no targeted binding
        if ($target === null || !isset($this->targetedSingletons[$target])) {
            if (!isset($this->universalSingletons[$interface])) {
                return null;
            }

            return $this->universalSingletons[$interface];
        }

        if (!isset($this->targetedSingletons[$target][$interface])) {
            return null;
        }

        return $this->targetedSingletons[$target][$interface];
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
    ) : array
    {
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
                if ($class !== null && isset($this->targetedBindingTypeMappings[$class])
                    && isset($this->targetedBindingTypeMappings[$class][$parameterClassName])
                ) {
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
     * Stops using a target
     */
    protected function stopTargeting()
    {
        array_pop($this->targetStack);
    }

    /**
     * Gets whether or not we're using a target
     *
     * @return bool True if we're using a target, otherwise false
     */
    protected function usingTarget() : bool
    {
        return count($this->targetStack) > 0;
    }

    /**
     * Gets the binding type (and target, if there was one) for an interface
     *
     * @param string $interface The interface whose binding type we want
     * @param bool $fallBackToUniversalBindings Whether or not to fall back to universal bindings
     * @return array|null An array whose first item is the binding type and second is the target (null if universal)
     *      Null is returned if there is no binding type
     */
    private function getBindingData(string $interface, bool $fallBackToUniversalBindings)
    {
        if ($this->usingTarget()) {
            $target = $this->getCurrentTarget();

            if (isset($this->targetedBindingTypeMappings[$target])
                && isset($this->targetedBindingTypeMappings[$target][$interface])
            ) {
                return [$this->targetedBindingTypeMappings[$target][$interface], $target];
            }

            if (!$fallBackToUniversalBindings) {
                return null;
            }
        }

        // If there was no targeted binding, then default to universal bindings
        if (!isset($this->universalBindingTypeMappings[$interface])) {
            return null;
        }

        return [$this->universalBindingTypeMappings[$interface], null];
    }

    /**
     * Maps a targeted binding to the type of binding
     *
     * @param string $interface The interface
     * @param string $target The target
     * @param string $type The binding type
     */
    private function mapTargetedBindingType(string $interface, string $target, string $type)
    {
        if (!isset($this->targetedBindingTypeMappings[$target])) {
            $this->targetedBindingTypeMappings[$target] = [];
        }

        $this->targetedBindingTypeMappings[$target][$interface] = $type;
    }

    /**
     * Maps a universal binding to the type of binding
     *
     * @param string $interface The interface
     * @param string $type The binding type
     */
    private function mapUniversalBindingType(string $interface, string $type)
    {
        $this->universalBindingTypeMappings[$interface] = $type;
    }
}