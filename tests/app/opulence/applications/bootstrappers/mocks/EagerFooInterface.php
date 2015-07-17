<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines an interface for a class that's used by bootstrappers
 */
namespace Opulence\Tests\Applications\Bootstrappers\Mocks;

interface EagerFooInterface
{
    /**
     * Gets the name of the concrete class
     *
     * @return string The name of the class
     */
    public function getClass();
}