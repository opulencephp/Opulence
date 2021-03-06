<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Ioc;

/**
 * Defines a class binding
 */
class ClassBinding implements IBinding
{
    /** @var string The name of the concrete class */
    private $concreteClass = '';
    /** @var array The list of constructor primitives */
    private $constructorPrimitives = [];
    /** @var bool Whether or not the class should be resolved as a singleton */
    private $resolveAsSingleton = false;

    /**
     * @param string $concreteClass The name of the concrete class
     * @param array $constructorPrimitives The list of constructor primitives
     * @param bool $resolveAsSingleton Whether or not to resolve as a singleton
     */
    public function __construct(string $concreteClass, array $constructorPrimitives, bool $resolveAsSingleton)
    {
        $this->concreteClass = $concreteClass;
        $this->constructorPrimitives = $constructorPrimitives;
        $this->resolveAsSingleton = $resolveAsSingleton;
    }

    /**
     * @return string
     */
    public function getConcreteClass() : string
    {
        return $this->concreteClass;
    }

    /**
     * @return array
     */
    public function getConstructorPrimitives() : array
    {
        return $this->constructorPrimitives;
    }

    /**
     * @return bool
     */
    public function resolveAsSingleton() : bool
    {
        return $this->resolveAsSingleton;
    }
}
