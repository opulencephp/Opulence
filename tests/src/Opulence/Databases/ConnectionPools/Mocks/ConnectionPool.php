<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Databases\ConnectionPools\Mocks;

use Opulence\Databases\ConnectionPools\ConnectionPool as BaseConnectionPool;
use Opulence\Databases\Server;

/**
 * Mocks the connection pool class for use in testing
 */
class ConnectionPool extends BaseConnectionPool
{
    /**
     * @inheritdoc
     */
    protected function setReadConnection(Server $preferredServer = null)
    {
        if ($preferredServer !== null) {
            $this->readConnection = $this->getConnection('custom', $preferredServer);
        } else {
            $this->readConnection = $this->getConnection('master', $this->getMaster());
        }
    }

    /**
     * @inheritdoc
     */
    protected function setWriteConnection(Server $preferredServer = null)
    {
        if ($preferredServer !== null) {
            $this->writeConnection = $this->getConnection('custom', $preferredServer);
        } else {
            $this->writeConnection = $this->getConnection('master', $this->getMaster());
        }
    }
}
