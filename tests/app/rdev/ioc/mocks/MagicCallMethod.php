<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a class that uses a __call magic method
 */
namespace RDev\Tests\IoC\Mocks;

class MagicCallMethod
{
    /**
     * Handles non-existent methods
     *
     * @param string $name The name of the method called
     * @param array $arguments The arguments
     */
    public function __call($name, array $arguments)
    {
        return;
    }
}