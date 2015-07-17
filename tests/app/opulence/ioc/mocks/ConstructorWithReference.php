<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a class that takes in a reference in its constructor
 */
namespace Opulence\Tests\IoC\Mocks;

class ConstructorWithReference
{
    /** @var IFoo The object passed into the constructor */
    private $foo = null;

    /**
     * @param IFoo $foo The object to use
     */
    public function __construct(IFoo &$foo)
    {
        $this->foo = $foo;
    }

    /**
     * @return IFoo
     */
    public function getFoo()
    {
        return $this->foo;
    }
}