<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks a controller that doesn't extend the base controller
 */
namespace RDev\Tests\Controllers\Mocks;

class InvalidController
{
    /**
     * A dummy method that does nothing
     *
     * @return string A dummy string
     */
    public function foo()
    {
        return "fooWasCalled";
    }
} 