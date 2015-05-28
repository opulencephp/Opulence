<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a class that implement the foo interface
 */
namespace RDev\Tests\Applications\Bootstrappers\Mocks;

class LazyConcreteFoo implements LazyFooInterface
{
    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return self::class;
    }
}