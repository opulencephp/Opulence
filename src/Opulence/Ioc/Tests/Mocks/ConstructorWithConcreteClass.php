<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Tests\Mocks;

/**
 * Mocks a class that takes in a concrete class in its constructor
 */
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
