<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines an interface for a class that's used by bootstrappers
 */
namespace RDev\Tests\Applications\Bootstrappers\Mocks;

interface LazyFooInterface
{
    /**
     * Gets the name of the concrete class
     *
     * @return string The name of the class
     */
    public function getClass();
}