<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the connection pool class for use in testing
 */
namespace RDev\Tests\Models\Databases\SQL\Mocks;
use RDev\Models\Databases\SQL;

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
            // We try to only read from the master as a last resort
            if($this->getMaster() == null)
            {
                throw new \RuntimeException("No master specified");
            }

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
            // We try to only read from the master as a last resort
            if($this->getMaster() == null)
            {
                throw new \RuntimeException("No master specified");
            }

            $this->writeConnection = $this->getConnection("master", $this->getMaster());
        }
    }
} 