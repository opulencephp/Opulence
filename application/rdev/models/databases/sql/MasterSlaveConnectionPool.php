<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a pool of master/slave servers
 */
namespace RDev\Models\Databases\SQL;

class MasterSlaveConnectionPool extends ConnectionPool
{
    /**
     * @param ConnectionFactory $connectionFactory The factory to use to create database connections
     * @param Server $master The master server
     * @param Server|Server[] $slaves The list of slave servers
     */
    public function __construct(ConnectionFactory $connectionFactory, Server $master = null, $slaves = [])
    {
        parent::__construct($connectionFactory);

        if($master !== null)
        {
            $this->setMaster($master);
        }

        if(!is_array($slaves))
        {
            $slaves = [$slaves];
        }

        /** @var Server $slave */
        foreach($slaves as $slave)
        {
            $this->addSlave($slave);
        }
    }

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
     * @return Server[]
     */
    public function getSlaves()
    {
        return array_column($this->config["slaves"], "server");
    }

    /**
     * {@inheritdoc}
     * The configuration can also contain an entry for "slaves" => [arrays_of_slave_server_data]
     */
    public function initFromConfig(array $config)
    {
        parent::initFromConfig($config);

        if(isset($config["slaves"]))
        {
            foreach($config["slaves"] as $slaveConfig)
            {
                $this->addServer("slaves", $this->serverFactory->createFromConfig($slaveConfig));
            }
        }
    }

    /**
     * Removes the input slave if it is in the list of slaves
     *
     * @param Server $slave The slave to remove
     */
    public function removeSlave(Server $slave)
    {
        $slaveHashId = spl_object_hash($slave);

        if(isset($this->config["slaves"][$slaveHashId]))
        {
            unset($this->config["slaves"][$slaveHashId]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig()
    {
        return [
            "master" => ["server" => null, "connection" => null],
            "slaves" => [],
            "custom" => []
        ];
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
        elseif(count($this->config["slaves"]) > 0)
        {
            // Randomly pick a slave
            $selectedSlave = $this->config["slaves"][array_rand($this->config["slaves"])]["server"];
            $this->readConnection = $this->getConnection("slaves", $selectedSlave);
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