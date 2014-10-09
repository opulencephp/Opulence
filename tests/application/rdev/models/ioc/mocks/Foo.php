<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a class that implements an interface for use in IoC tests
 */
namespace RDev\Tests\Models\IoC\Mocks;

class Foo implements IFoo
{
    /** @var IPerson A dependency */
    private $person = null;

    public function __construct(IPerson $person)
    {
        $this->person = $person;
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return __CLASS__;
    }
} 