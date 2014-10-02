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
     * @param string $interfaceName The name of the interface to bind to
     * @param string $concreteClassName The name of the concrete class to bind
     * @param string|null $targetClass The name of the target class to bind on, or null if binding to all classes
     */
    public function bind($interfaceName, $concreteClassName, $targetClass = null);

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
    public function createNew($component, $constructorPrimitives = [], $methodCalls = []);

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
    public function createSingleton($component, $constructorPrimitives = [], $methodCalls = []);
} 