<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the inversion of control container
 */
namespace RDev\Models\IoC;
use Dice;

class Container implements IContainer
{
    /** @var Dice\Dice */
    private $dice = null;
    /** @var array The bindings of class/interface names to concrete class names for all classes */
    private $universalBindings = [];
    /** @var array The name of a target class to its bindings of class/interface names to concrete class names */
    private $classBindings = [];
    /** @var array The array of class names to their singleton instances and constructor primitives */
    private $singletons = [];

    public function __construct()
    {
        $this->dice = new Dice\Dice();
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function createNew($component, $constructorPrimitives = [], $methodCalls = [])
    {
        return $this->create($component, $constructorPrimitives, true, $methodCalls);
    }

    /**
     * {@inheritdoc}
     */
    public function createSingleton($component, $constructorPrimitives = [], $methodCalls = [])
    {
        /**
         * The issue with Dice is that creating a singleton followed by a new instance wipes out the singleton
         * So, we must keep track of singletons manually
         */
        $concreteClassName = $this->getConcreteClassName($component);

        if(isset($this->singletons[$concreteClassName])
            && $this->singletons[$concreteClassName]["constructorPrimitives"] == $constructorPrimitives
            && $this->singletons[$concreteClassName]["methodCalls"] == $methodCalls
        )
        {
            return $this->singletons[$concreteClassName]["instance"];
        }

        $instance = $this->create($component, $constructorPrimitives, false, $methodCalls);

        // Track this instance for future reference
        $this->singletons[$concreteClassName] = [
            "instance" => $instance,
            "constructorPrimitives" => $constructorPrimitives,
            "methodCalls" => $methodCalls
        ];

        return $instance;
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
     *      ]
     * @return mixed The instantiated component
     */
    private function create($component, $constructorPrimitives, $forceNewInstance, $methodCalls)
    {
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

        // Set the method parameters
        foreach($methodCalls as $methodName => $parameters)
        {
            $rule->call[] = [$methodName, $parameters];
        }

        $concreteClassName = $this->getConcreteClassName($component);
        $this->dice->addRule($concreteClassName, $rule);

        return $this->dice->create($concreteClassName, $constructorPrimitives, $forceNewInstance);
    }

    /**
     * Gets the name of the concrete class bound to an abstract class/interface
     *
     * @param string $component The name of the abstract class/interface whose concrete class we're looking for
     * @return string The name of the concrete class bound to the component
     *      If the input was a concrete class, then it's returned
     */
    private function getConcreteClassName($component)
    {
        return isset($this->universalBindings[$component]) ? $this->universalBindings[$component] : $component;
    }
} 