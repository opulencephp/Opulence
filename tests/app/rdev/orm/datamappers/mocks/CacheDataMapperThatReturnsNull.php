<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Mocks a cache data mapper that returns null
 */
namespace RDev\Tests\ORM\DataMappers\Mocks;

class CacheDataMapperThatReturnsNull extends CacheDataMapper
{
    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return null;
    }
}