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
 * Mocks a class with setters for use in IoC tests
 */
class ConstructorWithSetters
{
    /** @var string A primitive */
    private $primitive = '';
    /** @var IFoo An interface dependency */
    private $interface;
    /** @var Bar A concrete dependency */
    private $concrete;

    /**
     * @return Bar
     */
    public function getConcrete(): Bar
    {
        return $this->concrete;
    }

    /**
     * @return IFoo
     */
    public function getInterface(): IFoo
    {
        return $this->interface;
    }

    /**
     * @return string
     */
    public function getPrimitive(): string
    {
        return $this->primitive;
    }

    /**
     * @param IFoo $interface The dependency to set
     * @param mixed $primitive The primitive to set
     */
    public function setBoth(IFoo $interface, $primitive): void
    {
        $this->setInterface($interface);
        $this->setPrimitive($primitive);
    }

    /**
     * @param Bar $concrete
     */
    public function setConcrete($concrete): void
    {
        $this->concrete = $concrete;
    }

    /**
     * @param IFoo $interface
     */
    public function setInterface(IFoo $interface): void
    {
        $this->interface = $interface;
    }

    /**
     * @param string $foo
     */
    public function setPrimitive($foo): void
    {
        $this->primitive = $foo;
    }
}
