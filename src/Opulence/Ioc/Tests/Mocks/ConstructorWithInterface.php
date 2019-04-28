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
 * Mocks a class that takes in an interface in its constructor
 */
class ConstructorWithInterface
{
    /** @var IFoo The object passed into the constructor */
    private $foo;

    /**
     * @param IFoo $foo The object to use
     */
    public function __construct(IFoo $foo)
    {
        $this->foo = $foo;
    }

    /**
     * @return IFoo
     */
    public function getFoo(): IFoo
    {
        return $this->foo;
    }
}
