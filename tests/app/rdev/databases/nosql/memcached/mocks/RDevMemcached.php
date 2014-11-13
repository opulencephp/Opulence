<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the Memcached class for use in testing
 */
namespace RDev\Tests\Databases\NoSQL\Memcached\Mocks;
use RDev\Databases\NoSQL\Memcached as MemcachedNamespace;
use RDev\Databases\NoSQL\Memcached\Configs;

// To get around having to install Memcached just to run tests, include a mock Memcached class
if(!class_exists("Memcached"))
{
    require_once __DIR__ . "/Memcached.php";
}

class RDevMemcached extends MemcachedNamespace\RDevMemcached
{
    /**
     * {@inheritdoc}
     */
    public function __construct(MemcachedNamespace\TypeMapper $typeMapper)
    {
        $this->typeMapper = $typeMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function addServer($host, $port, $weight = 0)
    {
        $server = new Server();
        $server->setHost($host);
        $server->setPort($port);
        $server->setWeight($weight);
        $this->servers[] = $server;
    }
}