<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Ioc;

/**
 * Defines a class binding
 */
class ClassBinding
{
    /** @var string The name of the concrete class */
    private $concreteClass = "";
    /** @var array The list of constructor primitives */
    private $constructorPrimitives = [];

    /**
     * @param string $concreteClass The name of the concrete class
     * @param array $constructorPrimitives The list of constructor primitives
     */
    public function __construct(string $concreteClass, array $constructorPrimitives = [])
    {
        $this->concreteClass = $concreteClass;
        $this->constructorPrimitives = $constructorPrimitives;
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
}