<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a class that takes in primitives with default values in its constructor
 */
namespace Opulence\Tests\IoC\Mocks;

class ConstructorWithDefaultValuePrimitives
{
    /** @var string A primitive stored by this class */
    private $foo = "";
    /** @var string A primitive stored by this class */
    private $bar = "";

    /**
     * @param string $foo A primitive to store in this class
     * @param string $bar A primitive to store in this class
     */
    public function __construct($foo, $bar = "bar")
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    /**
     * @return string
     */
    public function getBar()
    {
        return $this->bar;
    }

    /**
     * @return string
     */
    public function getFoo()
    {
        return $this->foo;
    }
} 