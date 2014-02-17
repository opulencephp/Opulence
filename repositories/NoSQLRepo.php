<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a repository that uses a NoSQL database as a storage method
 */
namespace RamODev\Repositories;
use RamODev\Databases\NoSQL\Redis;

class NoSQLRepo
{
    /** @var Redis\Database The Redis database to use for queries */
    protected $redisDatabase = null;

    /**
     * @param Redis\Database $redisDatabase The database to use for queries
     */
    public function __construct(Redis\Database $redisDatabase)
    {
        $this->redisDatabase = $redisDatabase;
    }
} 