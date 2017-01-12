<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Tests\Ioc\Mocks;

/**
 * Mocks a class with an interface in its constructor and setters for use in IoC tests
 */
class ConstructorWithInterfaceAndSetters
{
    /** @var IFoo A dependency */
    private $constructorDependency = null;
    /** @var IPerson A dependency */
    private $setterDependency = null;

    public function __construct(IFoo $foo)
    {
        $this->constructorDependency = $foo;
    }

    /**
     * @return IFoo
     */
    public function getConstructorDependency()
    {
        return $this->constructorDependency;
    }

    /**
     * @return IPerson
     */
    public function getSetterDependency()
    {
        return $this->setterDependency;
    }

    /**
     * @param IPerson $setterDependency
     */
    public function setSetterDependency(IPerson $setterDependency)
    {
        $this->setterDependency = $setterDependency;
    }
}
