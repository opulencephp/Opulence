<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a class with a static method for use in testing
 */
namespace Opulence\Tests\Views\Compilers\SubCompilers\Mocks;

class ClassWithStaticMethod
{
    /**
     * Gets a string
     *
     * @return string Returns "bar"
     */
    public static function foo()
    {
        return "bar";
    }
}