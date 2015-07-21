<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a class that implements an interface for use in IoC tests
 */
namespace Opulence\Tests\IoC\Mocks;

class Foo implements IFoo
{
    /** @var IPerson A dependency */
    private $person = null;

    public function __construct(IPerson $person)
    {
        $this->person = $person;
    }

    /**
     * @inheritdoc
     */
    public function getClassName()
    {
        return __CLASS__;
    }
} 