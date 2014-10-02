<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the inversion of control container
 */
namespace RDev\Models\IoC;
use Dice;

class Container
{
    /** @var Dice\Dice */
    private $dice = null;
    /** @var array The bindings of class/interface names to concrete class names for all classes */
    private $universalBindings = [];
    /** @var array The name of a target class to its bindings of class/interface names to concrete class names */
    private $classBindings = [];

    public function __construct()
    {
        $this->dice = new Dice\Dice();
    }

    /**
     * Binds a class to an interface or abstract class
     *
     * @param string $interfaceName The name of the interface to bind to
     * @param string $concreteClassName The name of the concrete class to bind
     * @param string|null $targetClass The name of the target class to bind on, or null if binding to all classes
     */
    public function bind($interfaceName, $concreteClassName, $targetClass = null)
    {
        if($targetClass === null)
        {
            $this->universalBindings[$interfaceName] = $concreteClassName;
        }
        else
        {
            if(!isset($this->classBindings[$targetClass]))
            {
                $this->classBindings[$targetClass] = [];
            }

            $this->classBindings[$targetClass][$interfaceName] = $concreteClassName;
        }
    }

    /**
     * Creates a new instance of the input class name
     *
     * @param string $component The name of the component to instantiate
     * @param array $constructorPrimitives The primitive parameter values to pass into the constructor
     * @param array $methodCalls The array of method calls and their primitive parameter values
     *      Should be structured like so:
     *      [
     *          NAME_OF_METHOD => [VALUES_OF_PRIMITIVE_PARAMETERS],
     *          ...
     *      ]
     * @return mixed A new instance of the input class
     */
    public function createNew($component, $constructorPrimitives = [], $methodCalls = [])
    {
        return $this->create($component, $constructorPrimitives, true, $methodCalls);
    }

    /**
     * Creates a shared instance of the input class name
     *
     * @param string $component The name of the component to instantiate
     * @param array $constructorPrimitives The primitive parameter values to pass into the constructor
     * @param array $methodCalls The array of method calls and their primitive parameter values
     *      Should be structured like so:
     *      [
     *          NAME_OF_METHOD => [VALUES_OF_PRIMITIVE_PARAMETERS],
     *          ...
     *      ]
     * @return mixed An instance of the input class
     */
    public function createShared($component, $constructorPrimitives = [], $methodCalls = [])
    {
        return $this->create($component, $constructorPrimitives, false, $methodCalls);
    }

    /**
     * Creates a component
     *
     * @param string $component The name of the component to create
     * @param array $constructorPrimitives The primitive parameter values to pass into the constructor
     * @param bool $forceNewInstance True if we want a new instance, otherwise false and we'll share it
     * @param array $methodCalls The array of method calls and their primitive parameter values
     *      Should be structured like so:
     *      [
     *          NAME_OF_METHOD => [VALUES_OF_PRIMITIVE_PARAMETERS],
     *          ...
     *
     * @return mixed The instantiated component
     */
    private function create($component, $constructorPrimitives, $forceNewInstance, $methodCalls)
    {
        $concreteClassName = $component;
        $rule = new Dice\Rule();
        $rule->shared = true;

        // Set the universal bindings first so they may be overridden by class-specific bindings
        foreach($this->universalBindings as $interfaceName => $className)
        {
            $rule->substitutions[$interfaceName] = new Dice\Instance($className);
        }

        if(isset($this->classBindings[$component]))
        {
            // Set class-specific bindings
            foreach($this->classBindings[$component] as $interfaceName => $className)
            {
                $rule->substitutions[$interfaceName] = new Dice\Instance($className);
            }
        }
        elseif(isset($this->universalBindings[$component]))
        {
            // The component was an interface name, so set it it to the concrete class name
            $concreteClassName = $this->universalBindings[$component];
        }

        // Set the method parameters
        foreach($methodCalls as $methodName => $parameters)
        {
            $rule->call[] = [$methodName, $parameters];
        }

        $this->dice->addRule($concreteClassName, $rule);

        return $this->dice->create($concreteClassName, $constructorPrimitives, $forceNewInstance);
    }
} 