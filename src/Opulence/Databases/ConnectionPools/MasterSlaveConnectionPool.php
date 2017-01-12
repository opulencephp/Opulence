<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Databases\ConnectionPools;

use Opulence\Databases\ConnectionPools\Strategies\ServerSelection\IServerSelectionStrategy;
use Opulence\Databases\ConnectionPools\Strategies\ServerSelection\RandomServerSelectionStrategy;
use Opulence\Databases\IDriver;
use Opulence\Databases\Server;

/**
 * Defines a pool of master/slave servers
 */
class MasterSlaveConnectionPool extends ConnectionPool
{
    /** @inheritdoc */
    protected $servers = [
        'master' => null,
        'slaves' => [],
        'custom' => []
    ];
    /** @var IServerSelectionStrategy The slave selection strategy */
    protected $slaveSelectionStrategy = null;

    /**
     * @inheritdoc
     * @param Server[] $slaves The list of slave servers to use
     * @param IServerSelectionStrategy $slaveSelectionStrategy The selection strategy to use to select slave servers
     */
    public function __construct(
        IDriver $driver,
        Server $master,
        array $slaves = [],
        array $driverOptions = [],
        array $connectionOptions = [],
        IServerSelectionStrategy $slaveSelectionStrategy = null
    ) {
        parent::__construct($driver, $master, $driverOptions, $connectionOptions);

        foreach ($slaves as $slave) {
            $this->addServer('slaves', $slave);
        }

        if ($slaveSelectionStrategy === null) {
            $slaveSelectionStrategy = new RandomServerSelectionStrategy();
        }

        $this->slaveSelectionStrategy = $slaveSelectionStrategy;
    }

    /**
     * Adds a slave to the list of slaves
     *
     * @param Server $slave The slave to add
     */
    public function addSlave(Server $slave)
    {
        $this->addServer('slaves', $slave);
    }

    /**
     * Adds slaves to the list
     *
     * @param Server[] $slaves The slaves to add
     */
    public function addSlaves(array $slaves)
    {
        foreach ($slaves as $slave) {
            $this->addSlave($slave);
        }
    }

    /**
     * @return Server[]
     */
    public function getSlaves() : array
    {
        return array_column($this->servers['slaves'], 'server');
    }

    /**
     * Removes the input slave if it is in the list of slaves
     *
     * @param Server $slave The slave to remove
     */
    public function removeSlave(Server $slave)
    {
        $slaveHashId = spl_object_hash($slave);

        if (isset($this->servers['slaves'][$slaveHashId])) {
            unset($this->servers['slaves'][$slaveHashId]);
        }
    }

    /**
     * @inheritdoc
     */
    protected function setReadConnection(Server $preferredServer = null)
    {
        if ($preferredServer !== null) {
            $this->readConnection = $this->getConnection('custom', $preferredServer);
        } elseif (count($this->servers['slaves']) > 0) {
            $selectedSlave = $this->slaveSelectionStrategy->select($this->getSlaves());
            $this->readConnection = $this->getConnection('slaves', $selectedSlave);
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
