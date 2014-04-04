<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for Redis repositories with SQL database backups
 */
namespace RamODev\Application\Shared\Repositories;

interface IRedisWithSQLBackupRepo
{
    /**
     * Synchronizes the Redis repository with the SQL repository
     *
     * @return bool True if successful, otherwise false
     */
    public function sync();
} 