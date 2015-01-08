<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the RDev Predis class for use in testing
 */
namespace RDev\Tests\Databases\NoSQL\Redis\Mocks;
use RDev\Databases\NoSQL\Redis;

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