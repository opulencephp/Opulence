<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the container class for use in testing
 */
namespace RDev\Tests\IoC\Mocks;
use RDev\IoC;

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
    public function getBinding($interface, $targetClass = null)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function isBound($interface, $targetClass = null)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function make($component, $forceNewInstance, array $constructorPrimitives = [], array $methodCalls = [])
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function makeNew($component, array $constructorPrimitives = [], array $methodCalls = [])
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function makeShared($component, array $constructorPrimitives = [], array $methodCalls = [])
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