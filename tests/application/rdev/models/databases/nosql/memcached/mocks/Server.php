<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the server class for use in testing
 */
namespace RDev\Tests\Models\Databases\NoSQL\Memcached\Mocks;
use RDev\Models\Databases\NoSQL\Memcached;

class Server extends Memcached\Server
{
    /** {@inheritdoc} */
    protected $host = "";
    /** {@inheritdoc} */
    protected $port = 11211;
    /** {@inheritdoc} */
    protected $weight = 0;
} 