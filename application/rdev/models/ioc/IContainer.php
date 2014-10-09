<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for dependency injection containers to implement
 */
namespace RDev\Models\IoC;

interface IContainer
{
    /**
     * Binds a class to an interface or abstract class
     *
     * @param string $interface The name of the interface to bind to
     * @param string|mixed $concreteClass Either the name of or an instance of the concrete class to bind
     * @param string|null $targetClass The name of the target class to bind on, or null if binding to all classes
     */
    public function bind($interface, $concreteClass, $targetClass = null);

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
    public function createNew($component, array $constructorPrimitives = [], array $methodCalls = []);

    /**
     * Creates a singleton instance of the input class name
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
    public function createSingleton($component, array $constructorPrimitives = [], array $methodCalls = []);

    /**
     * Gets the name of the concrete class bound to the interface
     *
     * @param string $interface The name of the interface whose binding we want
     * @param string|null $targetClass The name of the target class whose binding we want, or null for universal bindings
     * @return string|null The name of the concrete class bound to the interface if there is one, otherwise null
     */
    public function getBinding($interface, $targetClass = null);

    /**
     * Removes a binding from the container
     *
     * @param string $interface The name of the interface whose binding we're removing
     * @param string|null $targetClass The name of the target class whose binding we're removing, or null if it's universal
     */
    public function unbind($interface, $targetClass = null);
} 