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
 * Defines a class with a mix of interfaces and primitives in its constructor
 */
class ConstructorWithMixOfInterfacesAndPrimitives
{
    /** @var IFoo A dependency */
    private $foo = null;
    /** @var int A primitive */
    private $id = -1;
    /** @var IPerson A dependency */
    private $person = null;

    /**
     * @param IFoo $foo A dependency
     * @param int $id A primitive
     * @param IPerson $person A dependency
     */
    public function __construct(IFoo $foo, $id, IPerson $person)
    {
        $this->foo = $foo;
        $this->id = $id;
        $this->person = $person;
    }

    /**
     * @return IFoo
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return IPerson
     */
    public function getPerson()
    {
        return $this->person;
    }
}
