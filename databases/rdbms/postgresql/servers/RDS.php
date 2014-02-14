<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a specific server
 */
namespace RamODev\Databases\RDBMS\PostgreSQL\Servers;
use RamODev\Configs;

require_once(__DIR__ . "/Server.php");
require_once(__DIR__ . "/../../../../configs/StorageConfig.php");

class RDS extends Server
{
    protected $host = Configs\StorageConfig::RDS_HOST;
    protected $username = Configs\StorageConfig::RDS_USERNAME;
    protected $password = Configs\StorageConfig::RDS_PASSWORD;
    protected $databaseName = "dave";
    protected $displayName = "AWS Development";
} 