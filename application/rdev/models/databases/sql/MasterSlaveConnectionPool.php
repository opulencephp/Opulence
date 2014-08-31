<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a pool of master/slave servers
 */
namespace RDev\Models\Databases\SQL;
use RDev\Models\Databases\SQL\Configs;

class MasterSlaveConnectionPool extends ConnectionPool
{
    /** @inheritdoc} */
    protected $servers = [
        "master" => null,
        "slaves" => [],
        "custom" => []
    ];

    /**
     * Adds a slave to the list of slaves
     *
     * @param Server $slave The slave to add
     */
    public function addSlave(Server $slave)
    {
        $this->addServer("slaves", $slave);
    }

    /**
     * Adds slaves to the list
     *
     * @param Server[] $slaves The slaves to add
     */
    public function addSlaves(array $slaves)
    {
        foreach($slaves as $slave)
        {
            $this->addSlave($slave);
        }
    }

    /**
     * @return Server[]
     */
    public function getSlaves()
    {
        return array_column($this->servers["slaves"], "server");
    }

    /**
     * Removes the input slave if it is in the list of slaves
     *
     * @param Server $slave The slave to remove
     */
    public function removeSlave(Server $slave)
    {
        $slaveHashId = spl_object_hash($slave);

        if(isset($this->servers["slaves"][$slaveHashId]))
        {
            unset($this->servers["slaves"][$slaveHashId]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigFromArray(array $configArray)
    {
        return new Configs\MasterSlaveConnectionPoolConfig($configArray);
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
        elseif(count($this->servers["slaves"]) > 0)
        {
            // Randomly pick a slave
            $selectedSlave = $this->servers["slaves"][array_rand($this->servers["slaves"])]["server"];
            $this->readConnection = $this->getConnection("slaves", $selectedSlave);
        }
        else
        {
            $this->readConnection = $this->getConnection("master", $this->getMaster());
        }
    }

    /**
     * {@inheritdoc}
     * The server configuration can also contain an entry for "slaves" => [slave server objects]
     */
    protected function setServers(array $config)
    {
        parent::setServers($config);

        if(isset($config["slaves"]))
        {
            foreach($config["slaves"] as $slave)
            {
                $this->addServer("slaves", $slave);
            }
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