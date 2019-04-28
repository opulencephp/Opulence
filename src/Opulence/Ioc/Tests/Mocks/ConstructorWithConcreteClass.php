<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Tests\Mocks;

/**
 * Mocks a class that takes in a concrete class in its constructor
 */
class ConstructorWithConcreteClass
{
    /** @var Bar The object passed into the constructor */
    private $foo;

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
    public function getFoo(): Bar
    {
        return $this->foo;
    }
}
