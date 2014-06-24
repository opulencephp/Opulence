<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a pool of master/slave servers
 */
namespace RDev\Models\Databases\SQL;

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
     * The server configuration can also contain an entry for "slaves" => [slave server data]
     */
    protected function initServersFromConfig(array $config)
    {
        parent::initServersFromConfig($config);

        if(isset($config["servers"]["slaves"]))
        {
            foreach($config["servers"]["slaves"] as $slaveConfig)
            {
                $this->addServer("slaves", $this->serverFactory->createFromConfig($slaveConfig));
            }
        }
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