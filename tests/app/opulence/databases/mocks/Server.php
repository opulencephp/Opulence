<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the server class for use in testing
 */
namespace Opulence\Tests\Databases\SQL\Mocks;
use Opulence\Databases\Server as BaseServer;

class Server extends BaseServer
{
    protected $host = "1.2.3.4";
    protected $username = "foo";
    protected $password = "bar";
    protected $databaseName = "fakedatabase";
} 