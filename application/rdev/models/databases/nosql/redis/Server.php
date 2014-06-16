<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a Redis server
 */
namespace RDev\Models\Databases\NoSQL\Redis;
use RDev\Models\Databases;

abstract class Server extends Databases\Server
{
    /** {@inheritdoc} */
    protected $port = 6379;
}