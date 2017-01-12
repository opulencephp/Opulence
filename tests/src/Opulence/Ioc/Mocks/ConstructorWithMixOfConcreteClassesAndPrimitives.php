<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Tests\Ioc\Mocks;

/**
 * Defines a class with a mix of concrete classes and primitives in its constructor
 */
class ConstructorWithMixOfConcreteClassesAndPrimitives
{
    /** @var Bar A dependency */
    private $foo = null;
    /** @var int A primitive */
    private $id = -1;
    /** @var Dave A dependency */
    private $person = null;

    /**
     * @param Bar $foo A dependency
     * @param int $id A primitive
     * @param Dave $person A dependency
     */
    public function __construct(Bar $foo, $id, Dave $person)
    {
        $this->foo = $foo;
        $this->id = $id;
        $this->person = $person;
    }

    /**
     * @return Bar
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
     * @return Dave
     */
    public function getPerson()
    {
        return $this->person;
    }
}
