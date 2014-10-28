<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks a class with setters for use in IoC tests
 */
namespace RDev\Tests\IoC\Mocks;

class ConstructorWithSetters
{
    /** @var string A primitive */
    private $primitive = "";
    /** @var IFoo A dependency */
    private $dependency = null;

    /**
     * @return IFoo
     */
    public function getDependency()
    {
        return $this->dependency;
    }

    /**
     * @return string
     */
    public function getPrimitive()
    {
        return $this->primitive;
    }

    /**
     * @param IFoo $dependency
     */
    public function setDependency(IFoo $dependency)
    {
        $this->dependency = $dependency;
    }

    /**
     * @param string $foo
     */
    public function setPrimitive($foo)
    {
        $this->primitive = $foo;
    }
} 