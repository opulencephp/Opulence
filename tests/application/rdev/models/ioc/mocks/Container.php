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
    public function bind($interface, $concreteClass, $targetClass = null)
    {
        // Don't do anything
    }

    /**
     * {@inheritdoc}
     */
    public function createNew($component, array $constructorPrimitives = [], array $methodCalls = [])
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function createSingleton($component, array $constructorPrimitives = [], array $methodCalls = [])
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getBinding($interface, $targetClass = null)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function unbind($interface, $targetClass = null)
    {
        // Don't do anything
    }
} 