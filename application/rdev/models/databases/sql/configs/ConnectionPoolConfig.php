<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the connection pool config
 */
namespace RDev\Models\Databases\SQL\Configs;
use RDev\Models\Configs;

class ConnectionPoolConfig extends Configs\Config
{
    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        return $this->hasRequiredFields($this->configArray, [
            "driver" => null,
            "servers" => [
                "master" => null
            ]
        ]);
    }
} 