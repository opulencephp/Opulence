<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Ioc\Mocks;

/**
 * Mocks a class that takes in a reference in its constructor
 */
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

    /**
     * @param IFoo $foo The object to use
     */
    public function setFoo(IFoo &$foo)
    {
        $this->foo = $foo;
    }
}
