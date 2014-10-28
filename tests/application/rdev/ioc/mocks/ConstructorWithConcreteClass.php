<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks a class that takes in a concrete class in its constructor
 */
namespace RDev\Tests\IoC\Mocks;

class ConstructorWithConcreteClass
{
    /** @var Bar The object passed into the constructor */
    private $foo = null;

    /**
     * @param Bar $foo The object to use
     */
    public function __construct(Bar $foo)
    {
        $this->foo = $foo;
    }

    /**
     * @return Bar
     */
    public function getFoo()
    {
        return $this->foo;
    }
} 