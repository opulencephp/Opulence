<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a simple interface for use in testing
 */
namespace Opulence\Tests\IoC\Mocks;

interface IFoo
{
    /**
     * Gets the name of the concrete class
     *
     * @return string The name of the concrete class
     */
    public function getClassName();
} 