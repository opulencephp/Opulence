<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for Redis repositories with SQL database backups
 */
namespace RDev\Models\Repositories;

interface IRedisWithSQLBackupRepo
{
    /**
     * Synchronizes the Redis database with the SQL database
     *
     * @return bool True if successful, otherwise false
     */
    public function sync();
} 