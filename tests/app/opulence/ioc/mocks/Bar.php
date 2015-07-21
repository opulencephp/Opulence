<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a class that implements a simple interface
 */
namespace Opulence\Tests\IoC\Mocks;

class Bar implements IFoo
{
    /**
     * @inheritdoc
     */
    public function getClassName()
    {
        return __CLASS__;
    }
} 