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
     * @throws Exceptions\RepoException Thrown if there was an error syncing the data mappers
     */
    public function sync();
} 