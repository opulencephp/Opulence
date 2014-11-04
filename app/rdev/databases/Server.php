<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides a skeleton for database servers to extend
 */
namespace RDev\Databases;

abstract class Server
{
    /** @var string The host of this server */
    protected $host = "";
    /** @var int The port this server listens on */
    protected $port;

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }
} 