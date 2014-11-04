<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the server class for use in testing
 */
namespace RDev\Tests\Databases\NoSQL\Memcached\Mocks;
use RDev\Databases\NoSQL\Memcached;

class Server extends Memcached\Server
{
    /** {@inheritdoc} */
    protected $host = "";
    /** {@inheritdoc} */
    protected $port = 11211;
    /** {@inheritdoc} */
    protected $weight = 0;
} 