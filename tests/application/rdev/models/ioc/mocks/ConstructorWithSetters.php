<?php
/**
 * Copyright (C) 2014 David Young
 *
 *
 */
namespace RDev\Tests\Models\IoC\Mocks;

class ConstructorWithSetters
{
    /** @var string The primitive stored by this class */
    private $foo = "";

    /**
     * @return string
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @param string $foo
     */
    public function setFoo($foo)
    {
        $this->foo = $foo;
    }
} 