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
 * Mocks a class that takes in primitives in its constructor
 */
class ConstructorWithPrimitives
{
    /** @var string A primitive stored by this class */
    private $foo;
    /** @var string A primitive stored by this class */
    private $bar;

    /**
     * @param string $foo A primitive to store in this class
     * @param string $bar A primitive to store in this class
     */
    public function __construct($foo, $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    /**
     * @return string
     */
    public function getBar(): string
    {
        return $this->bar;
    }

    /**
     * @return string
     */
    public function getFoo(): string
    {
        return $this->foo;
    }
}
