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

/**
 * Defines the dependency injection container
 */
class NewContainer implements INewContainer
{
    /** @var array The stack of targets */
    protected $targetStack = [];
    /** @var array The mapping of interfaces to factories */
    protected $universalFactories = [];
    /** @var array The mapping of interfaces to an array of instances */
    protected $universalInstances = [];
    /** @var array The mapping of interfaces to an array of prototype class bindings */
    protected $universalPrototypes = [];
    /** @var array The mapping of interfaces to an array of singleton class bindings */
    protected $universalSingletons = [];
    /** @var array The mapping of targets to interfaces to factories */
    protected $targetedFactories = [];
    /** @var array The mapping of targets to interfaces to an array of instances */
    protected $targetedInstances = [];
    /** @var array The mapping of targets to interfaces to an array of prototypes and constructor primitives */
    protected $targetedPrototypes = [];
    /** @var array The mapping of targets to interfaces to an array of singletons and constructor primitives */
    protected $targetedSingletons = [];

    public function __sleep()
    {
        return "";
    }

    /**
     * @inheritdoc
     */
    public function bindFactory($interfaces, callable $factory)
    {
        foreach ((array)$interfaces as $interface) {
            if ($this->usingTarget()) {
                $target = $this->getCurrentTarget();

                if (!isset($this->targetedFactories[$target])) {
                    $this->targetedFactories[$target] = [];
                }

                $this->targetedFactories[$target][$interface] = $factory;
            } else {
                $this->universalFactories[$interface] = $factory;
            }
        }

        $this->stopTargeting();
    }

    /**
     * @inheritdoc
     */
    public function bindInstance($interfaces, $instance)
    {
        foreach ((array)$interfaces as $interface) {
            if ($this->usingTarget()) {
                $target = $this->getCurrentTarget();

                if (!isset($this->targetedInstances[$target])) {
                    $this->targetedInstances[$target] = [];
                }

                $this->targetedInstances[$target][$interface] = $instance;
            } else {
                $this->universalInstances[$interface] = $instance;
            }
        }

        $this->stopTargeting();
    }

    /**
     * @inheritdoc
     */
    public function bindPrototype($interfaces, string $concreteClass = null, array $primitives = [])
    {
        foreach ((array)$interfaces as $interface) {
            $binding = new ClassBinding($concreteClass ?? $interface, $primitives);

            if ($this->usingTarget()) {
                $target = $this->getCurrentTarget();

                if (!isset($this->targetedPrototypes[$target])) {
                    $this->targetedPrototypes[$target] = [];
                }

                $this->targetedPrototypes[$target][$interface] = $binding;
            } else {
                $this->universalPrototypes[$interface] = $binding;
            }
        }

        $this->stopTargeting();
    }

    /**
     * @inheritdoc
     */
    public function bindSingleton($interfaces, string $concreteClass = null, array $primitives = [])
    {
        foreach ((array)$interfaces as $interface) {
            $binding = new ClassBinding($concreteClass ?? $interface, $primitives);

            if ($this->usingTarget()) {
                $target = $this->getCurrentTarget();

                if (!isset($this->targetedSingletons[$target])) {
                    $this->targetedSingletons[$target] = [];
                }

                $this->targetedSingletons[$target][$interface] = $binding;
            } else {
                $this->universalSingletons[$interface] = $binding;
            }
        }

        $this->stopTargeting();
    }

    /**
     * @inheritdoc
     */
    public function callClosure(callable $closure, array $primitives = [])
    {
        $unresolvedParameters = (new ReflectionFunction($closure))->getParameters();
        $resolvedParameters = $this->resolveParameters(null, $unresolvedParameters, $primitives);

        return call_user_func_array($closure, $resolvedParameters);
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
        $resolvedParameters = $this->resolveParameters(get_class($instance), $unresolvedParameters, $primitives);

        return call_user_func_array([$instance, $methodName], $resolvedParameters);
    }

    /**
     * @inheritdoc
     */
    public function for (string $targetClass) : INewContainer
    {
        $this->targetStack[] = $targetClass;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hasBinding(string $interface) : bool
    {
        $target = $this->usingTarget() ? $this->getCurrentTarget() : $interface;
        $hasBinding = $this->getFactory($interface, $target) || $this->getInstance($interface, $target) ||
            $this->getPrototype($interface, $target) || $this->getSingleton($interface, $target);
        $this->stopTargeting();

        return $hasBinding;
    }

    /**
     * @inheritdoc
     */
    public function resolve(string $interface)
    {
        $target = $this->usingTarget() ? $this->getCurrentTarget() : $interface;

        /**
         * This must always take precedence and always come before checking for singletons
         * This is because resolved singletons are registered as instances
         */
        if (($instance = $this->getInstance($interface, $target)) !== null) {
            $this->stopTargeting();

            return $instance;
        }

        if (($classBinding = $this->getSingleton($interface, $target)) !== null) {
            $instance = $this->resolveClass(
                $classBinding->getConcreteClass(),
                $classBinding->getConstructorPrimitives()
            );

            $this->universalInstances[$interface] = $instance;
            $this->stopTargeting();

            return $instance;
        }

        if (($factory = $this->getFactory($interface, $target)) !== null) {
            $this->stopTargeting();

            return $this->callClosure($factory);
        }

        if (($classBinding = $this->getPrototype($interface, $target)) !== null) {
            $this->stopTargeting();

            return $this->resolveClass(
                $classBinding->getConcreteClass(),
                $classBinding->getConstructorPrimitives()
            );
        }

        // As a last-ditch effort, try instantiating the class directly
        return $this->resolveClass($interface);
    }

    /**
     * @inheritdoc
     */
    public function unbind($interface)
    {
        if ($this->usingTarget()) {
            if (isset($this->targetedFactories[$this->getCurrentTarget()])) {
                unset($this->targetedFactories[$this->getCurrentTarget()][$interface]);
            }

            if (isset($this->targetedInstances[$this->getCurrentTarget()])) {
                unset($this->targetedInstances[$this->getCurrentTarget()][$interface]);
            }

            if (isset($this->targetedPrototypes[$this->getCurrentTarget()])) {
                unset($this->targetedPrototypes[$this->getCurrentTarget()][$interface]);
            }

            if (isset($this->targetedSingletons[$this->getCurrentTarget()])) {
                unset($this->targetedSingletons[$this->getCurrentTarget()][$interface]);
            }
        } else {
            unset($this->universalFactories[$interface]);
            unset($this->universalInstances[$interface]);
            unset($this->universalPrototypes[$interface]);
            unset($this->universalSingletons[$interface]);
        }

        $this->stopTargeting();
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
     * @return callable|null The factory if there was one, otherwise null
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
                throw new IocException("$class is not instantiable");
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
                if ($class === null) {
                    $resolvedParameter = $this->resolve($parameter->getClass()->getName());
                } else {
                    $resolvedParameter = $this->for($class)
                        ->resolve($parameter->getClass()->getName());
                    $this->stopTargeting();
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
}