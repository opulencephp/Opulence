<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the connection pool config reader
 */
namespace RDev\Models\Databases\SQL\Configs;
use RDev\Models\Configs;

class ConnectionPoolReader extends Configs\Reader
{
    /**
     * {@inheritdoc}
     */
    public function validateConfig(Configs\IConfig $config)
    {
        return $this->hasRequiredFields($config->toArray(), [
            "driver" => null,
            "servers" => [
                "master" => null
            ]
        ]);
    }
} 