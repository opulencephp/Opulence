<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the RDev Predis class for use in testing
 */
namespace RDev\Tests\Models\Databases\NoSQL\Redis\Mocks;
use RDev\Models\Databases\NoSQL\Redis;
use RDev\Models\Databases\NoSQL\Redis\Configs;

class RDevPredis extends Redis\RDevPredis
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Redis\Server $server, Redis\TypeMapper $typeMapper)
    {
        $this->server = $server;
        $this->typeMapper = $typeMapper;
    }

    /**
     * We don't want to close the connection because there wasn't one, so do nothing
     */
    public function __destruct()
    {
        // Do nothing
    }
} 