<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides a skeleton for database servers to extend
 */
namespace RamODev\Application\Databases;

abstract class Server
{
    /** @var string The host of this server */
    protected $host = "";
    /** @var string The "nice" name of the server (ie English, readable name) */
    protected $displayName = "";

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @param string $ip
     */
    public function setHost($ip)
    {
        $this->host = $ip;
    }
} 