<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the container class for use in testing
 */
namespace RDev\Tests\Models\IoC\Mocks;
use RDev\Models\IoC;

class Container implements IoC\IContainer
{
    /**
     * {@inheritdoc}
     */
    public function bind($interfaceName, $concreteClassName, $targetClass = null)
    {
        // Don't do anything
    }

    /**
     * {@inheritdoc}
     */
    public function createNew($component, $constructorPrimitives = [], $methodCalls = [])
    {
        // Don't do anything
    }

    /**
     * {@inheritdoc}
     */
    public function createSingleton($component, $constructorPrimitives = [], $methodCalls = [])
    {
        // Don't do anything
    }
} 