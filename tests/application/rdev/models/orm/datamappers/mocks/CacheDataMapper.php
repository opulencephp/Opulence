<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the cache data mapper class for use in testing
 */
namespace RDev\Tests\Models\ORM\DataMappers\Mocks;
use RDev\Models;
use RDev\Models\ORM\Exceptions;
use RDev\Models\ORM\DataMappers;

class CacheDataMapper extends DataMapper implements DataMappers\ICacheDataMapper
{
    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->entities = [];
    }
} 