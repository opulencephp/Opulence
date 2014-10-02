<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks a class that implements a simple interface
 */
namespace RDev\Tests\Models\IoC\Mocks;

class Bar implements IFoo
{
    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return __CLASS__;
    }
} 