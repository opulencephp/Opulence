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
    protected function setWriteConnection(Server $preferredServer = null)
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