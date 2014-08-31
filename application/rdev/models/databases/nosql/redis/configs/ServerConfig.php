<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the server config
 */
namespace RDev\Models\Databases\NoSQL\Redis\Configs;
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
            "port" => null
        ])
        )
        {
            return false;
        }

        if(isset($this["password"]) && !is_string($this["password"]))
        {
            return false;
        }

        if(isset($this["databaseIndex"]) && !is_numeric($this["databaseIndex"]))
        {
            return false;
        }

        if(isset($this["connectionTimeout"]) && !is_numeric($this["connectionTimeout"]))
        {
            return false;
        }

        return true;
    }
} 