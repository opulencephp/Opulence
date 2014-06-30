<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the server class for use in testing
 */
namespace RDev\Tests\Models\Databases\NoSQL\Redis\Mocks;
use RDev\Models\Databases\NoSQL\Redis;

class Server extends Redis\Server
{
    /** {@inheritdoc} */
    protected $host = "";
    /** {@inheritdoc} */
    protected $port = 6379;
} 