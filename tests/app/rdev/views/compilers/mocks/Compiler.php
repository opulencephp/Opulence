<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Mocks a compiler for use in testing
 */
namespace RDev\Tests\Views\Compilers\Mocks;
use RDev\Views\Cache;
use RDev\Views\Compilers;
use RDev\Views\Filters;

class Compiler extends Compilers\Compiler
{
    /**
     * {@inheritdoc}
     * This mocks does not have any built-in template functions
     */
    public function __construct()
    {
        // Don't do anything
    }
}