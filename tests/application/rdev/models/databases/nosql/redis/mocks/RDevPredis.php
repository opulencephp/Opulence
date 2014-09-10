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
    public function __construct($config)
    {
        if(is_array($config))
        {
            $config = new Configs\ServerConfig($config);
        }

        $this->server = $config["servers"]["master"];
        $this->typeMapper = new Redis\TypeMapper();
    }

    /**
     * We don't want to close the connection because there wasn't one, so do nothing
     */
    public function __destruct()
    {
        // Do nothing
    }
} 