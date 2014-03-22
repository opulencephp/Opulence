<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a specific server
 */
namespace RamODev\Databases\SQL\PostgreSQL\Servers;
use RamODev\Configs;

class RDS extends Server
{
    protected $host = Configs\DatabaseConfig::RDS_HOST;
    protected $username = Configs\DatabaseConfig::RDS_USERNAME;
    protected $password = Configs\DatabaseConfig::RDS_PASSWORD;
    protected $databaseName = "dave";
    protected $displayName = "AWS Development";
} 