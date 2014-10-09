<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a class that implements an interface for use in IoC tests
 */
namespace RDev\Tests\Models\IoC\Mocks;

class Dave implements IPerson
{
    /**
     * {@inheritdoc}
     */
    public function getLastName()
    {
        return "Young";
    }
} 