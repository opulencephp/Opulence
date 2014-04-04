<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a specific server
 */
namespace RamODev\Application\TBA\Databases\SQL\PostgreSQL\Servers;
use RamODev\Application\Shared\Databases\SQL\PostgreSQL;
use RamODev\Application\TBA\Configs;

class RDS extends PostgreSQL\Server
{
    protected $host = Configs\DatabaseConfig::RDS_HOST;
    protected $username = Configs\DatabaseConfig::RDS_USERNAME;
    protected $password = Configs\DatabaseConfig::RDS_PASSWORD;
    protected $databaseName = "dave";
    protected $displayName = "AWS Development";
} 