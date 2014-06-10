<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a single server implementation of the connection pool, which can be used for basic, non-master/slave setups
 */
namespace RDev\Models\Databases\SQL;

class SingleServerConnectionPool extends ConnectionPool
{
    /**
     * @param IConnectionFactory $connectionFactory The factory to use to create database connections
     * @param Server $server The server to use
     */
    public function __construct(IConnectionFactory $connectionFactory, Server $server)
    {
        parent::__construct($connectionFactory);

        $this->setMaster($server);
    }

    /**
     * {@inheritdoc}
     */
    protected function setReadConnection(Server $preferredServer = null)
    {
        if($preferredServer !== null)
        {
            $this->readConnection = $this->getConnection("custom", $preferredServer);
        }
        else
        {
            $this->readConnection = $this->getConnection("master", $this->getMaster());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setWriteConnection(Server $preferredServer = null)
    {
        if($preferredServer !== null)
        {
            $this->writeConnection = $this->getConnection("custom", $preferredServer);
        }
        else
        {
            $this->writeConnection = $this->getConnection("master", $this->getMaster());
        }
    }
} 