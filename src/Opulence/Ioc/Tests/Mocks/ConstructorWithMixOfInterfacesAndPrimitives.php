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
 * Defines a class with a mix of interfaces and primitives in its constructor
 */
class ConstructorWithMixOfInterfacesAndPrimitives
{
    /** @var IFoo A dependency */
    private $foo;
    /** @var int A primitive */
    private $id;
    /** @var IPerson A dependency */
    private $person;

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
    public function getFoo(): IFoo
    {
        return $this->foo;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return IPerson
     */
    public function getPerson(): IPerson
    {
        return $this->person;
    }
}
