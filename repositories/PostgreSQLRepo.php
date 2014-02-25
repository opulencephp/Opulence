<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a repository that uses a PostgreSQL database as a storage method
 */
namespace RamODev\Repositories;
use RamODev\Databases\SQL;

class PostgreSQLRepo
{
    /** @var SQL\Database The relational database to use for queries */
    protected $sqlDatabase = null;

    /**
     * @param SQL\Database $sqlDatabase The database to use for queries
     */
    public function __construct(SQL\Database $sqlDatabase)
    {
        $this->sqlDatabase = $sqlDatabase;
    }
} 