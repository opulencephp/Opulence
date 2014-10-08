<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a mock controller for use in testing
 */
namespace RDev\Tests\Controllers\Mocks;
use RDev\Controllers;
use RDev\Tests\Models\Mocks;

class Controller extends Controllers\Controller
{
    /**
     * Mocks a method that takes in multiple parameters
     *
     * @param mixed $foo The first parameter
     * @param mixed $bar The second parameter
     * @return string The parameter names to their values
     */
    public function multipleParameters($foo, $bar)
    {
        return "foo:$foo, bar:$bar";
    }

    /**
     * Mocks a method that takes in multiple parameters with some default values
     *
     * @param mixed $foo The first parameter
     * @param mixed $bar The second parameter
     * @param mixed $blah The optional third parameter
     * @return string The parameter names to their values
     */
    public function multipleParametersWithDefaultValues($foo, $bar, $blah = "724")
    {
        return "foo:$foo, bar:$bar, blah:$blah";
    }

    /**
     * Mocks a method that takes in no parameters
     *
     * @return string An empty string
     */
    public function noParameters()
    {
        return "noParameters";
    }

    /**
     * Mocks a method that takes in a single parameter
     *
     * @param mixed $foo The parameter
     * @return string The parameter name to its value
     */
    public function oneParameter($foo)
    {
        return "foo:$foo";
    }

    /**
     * Mocks a method that does not return anything
     */
    public function returnsNothing()
    {
        // Don't do anything
    }

    /**
     * Mocks a protected method for use in testing
     *
     * @return string The name of the method
     */
    protected function protectedMethod()
    {
        return "protectedMethod";
    }

    /**
     * Mocks a private method for use in testing
     *
     * @return string The name of the method
     */
    private function privateMethod()
    {
        return "privateMethod";
    }
} 