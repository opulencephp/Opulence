<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the server class for use in testing
 */
namespace RDev\Tests\Databases\NoSQL\Memcached\Mocks;
use RDev\Memcached\Server as BaseServer;

class Server extends BaseServer
{
    /** {@inheritdoc} */
    protected $host = "";
    /** {@inheritdoc} */
    protected $port = 11211;
    /** {@inheritdoc} */
    protected $weight = 0;
} 