<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the server class for use in testing
 */
namespace RDev\Tests\Databases\SQL\Mocks;
use RDev\Databases\SQL;
use RDev\Databases\SQL\Providers;

class Server extends SQL\Server
{
    protected $host = "1.2.3.4";
    protected $username = "foo";
    protected $password = "bar";
    protected $databaseName = "fakedatabase";
} 