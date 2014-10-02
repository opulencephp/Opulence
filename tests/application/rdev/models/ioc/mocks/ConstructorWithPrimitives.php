<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks a class that takes in primitives in its constructor
 */
namespace RDev\Tests\Models\IoC\Mocks;

class ConstructorWithPrimitives
{
    /** @var string The primitive stored by this class */
    private $foo = "";

    /**
     * @param string $foo The primitive to store in this class
     */
    public function __construct($foo)
    {
        $this->foo = $foo;
    }

    /**
     * @return string
     */
    public function getFoo()
    {
        return $this->foo;
    }
} 