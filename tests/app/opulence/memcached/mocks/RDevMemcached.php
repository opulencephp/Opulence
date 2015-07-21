<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the Memcached class for use in testing
 */
namespace Opulence\Tests\Memcached\Mocks;
use Opulence\Memcached\OpulenceMemcached as BaseOpulenceMemcached;
use Opulence\Memcached\TypeMapper;

// To get around having to install Memcached just to run tests, include a mock Memcached class
if(!class_exists("Memcached"))
{
    require_once __DIR__ . "/Memcached.php";
}

class OpulenceMemcached extends BaseOpulenceMemcached
{
    /**
     * @inheritdoc
     */
    public function __construct(TypeMapper $typeMapper)
    {
        $this->typeMapper = $typeMapper;
    }

    /**
     * @inheritdoc
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