<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a class that implement the foo interface
 */
namespace RDev\Tests\Applications\Bootstrappers\Mocks;

class EagerConcreteFoo implements EagerFooInterface
{
    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return self::class;
    }
}