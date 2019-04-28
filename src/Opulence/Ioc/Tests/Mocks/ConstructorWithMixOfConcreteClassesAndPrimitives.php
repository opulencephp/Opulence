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
 * Defines a class with a mix of concrete classes and primitives in its constructor
 */
class ConstructorWithMixOfConcreteClassesAndPrimitives
{
    /** @var Bar A dependency */
    private $foo;
    /** @var int A primitive */
    private $id;
    /** @var Dave A dependency */
    private $person;

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
    public function getFoo(): Bar
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
     * @return Dave
     */
    public function getPerson(): Dave
    {
        return $this->person;
    }
}
