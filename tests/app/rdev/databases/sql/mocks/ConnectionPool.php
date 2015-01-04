<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the connection pool class for use in testing
 */
namespace RDev\Tests\Databases\SQL\Mocks;
use RDev\Databases\SQL;

class ConnectionPool extends SQL\ConnectionPool
{
    /**
     * {@inheritdoc}
     */
    protected function setReadConnection(SQL\Server $preferredServer = null)
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
    protected function setWriteConnection(SQL\Server $preferredServer = null)
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