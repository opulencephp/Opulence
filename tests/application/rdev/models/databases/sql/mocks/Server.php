<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the server class for use in testing
 */
namespace RDev\Tests\Models\Databases\SQL\Mocks;
use RDev\Models\Databases\SQL;

class Server extends SQL\Server
{
    protected $host = "1.2.3.4";
    protected $username = "foo";
    protected $password = "bar";
    protected $databaseName = "fakedatabase";
    protected $displayName = "Server Mock";
} 