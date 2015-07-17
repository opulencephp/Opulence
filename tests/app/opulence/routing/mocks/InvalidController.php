<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a controller that doesn't extend the base controller
 */
namespace Opulence\Tests\Routing\Mocks;

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