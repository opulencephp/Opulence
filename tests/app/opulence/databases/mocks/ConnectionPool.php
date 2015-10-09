<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the connection pool class for use in testing
 */
namespace Opulence\Tests\Databases\SQL\Mocks;

use Opulence\Databases\ConnectionPool as BaseConnectionPool;
use Opulence\Databases\Server;

class ConnectionPool extends BaseConnectionPool
{
    /**
     * @inheritdoc
     */
    protected function setReadConnection(Server $preferredServer = null)
    {
        if ($preferredServer !== null) {
            $this->readConnection = $this->getConnection("custom", $preferredServer);
        } else {
            $this->readConnection = $this->getConnection("master", $this->getMaster());
        }
    }

    /**
     * @inheritdoc
     */
    protected function setWriteConnection(Server $preferredServer = null)
    {
        if ($preferredServer !== null) {
            $this->writeConnection = $this->getConnection("custom", $preferredServer);
        } else {
            $this->writeConnection = $this->getConnection("master", $this->getMaster());
        }
    }
} 