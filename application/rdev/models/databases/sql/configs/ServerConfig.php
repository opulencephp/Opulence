<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the server config
 */
namespace RDev\Models\Databases\SQL\Configs;
use RDev\Models\Configs;

class ServerConfig extends Configs\Config
{
    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        if(!$this->hasRequiredFields($this->configArray, [
            "host" => null,
            "username" => null,
            "password" => null,
            "databaseName" => null,
        ])
        )
        {
            return false;
        }

        if(isset($this["port"]) && !is_numeric($this["port"]))
        {
            return false;
        }

        if(isset($this["charset"]) && !is_string($this["charset"]))
        {
            return false;
        }

        return true;
    }
} 