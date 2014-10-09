<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a class with a mix of interfaces and primitives in its constructor
 */
namespace RDev\Tests\Models\IoC\Mocks;

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