<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks another class that implements a simple interface
 */
namespace RDev\Tests\IoC\Mocks;

class Blah implements IFoo
{
    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return __CLASS__;
    }
} 