<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the connection class for use in testing
 */
namespace RDev\Tests\Databases\SQL\Mocks;
use RDev\Databases\SQL;
use RDev\Databases\SQL\Providers;

class Connection implements SQL\IConnection
{
    /** @var Providers\TypeMapper The type mapper used by this connection */
    private $typeMapper = null;
    /** @var Providers\Provider The provider used by this connection */
    private $provider = null;
    /** @var SQL\Server The server to connect to */
    private $server = null;
    /** @var bool Whether or not we're in a transaction */
    private $inTransaction = false;
    /** @var array The mapping of sequence names to last insert Ids */
    private $lastInsertIds = [];
    /** @var bool Whether or not this connection should fail on purpose */
    private $shouldFailOnPurpose = false;

    /**
     * @param SQL\Server $server The server to connect to
     */
    public function __construct(SQL\Server $server)
    {
        $this->typeMapper = new Providers\TypeMapper();
        $this->provider = new Providers\Provider();
        $this->server = $server;
    }

    /**
     * {@inheritdoc}
     */
    public function beginTransaction()
    {
        $this->inTransaction = true;
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        $this->inTransaction = false;

        if($this->shouldFailOnPurpose)
        {
            throw new \Exception("Commit failed");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function errorCode()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function errorInfo()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function exec($statement)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabaseProvider()
    {
        return $this->provider;
    }

    /**
     * {@inheritdoc}
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeMapper()
    {
        return $this->typeMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function inTransaction()
    {
        return $this->inTransaction;
    }

    /**
     * {@inheritdoc}
     */
    public function lastInsertId($sequenceName = null)
    {
        if(!isset($this->lastInsertIds[$sequenceName]))
        {
            $this->lastInsertIds[$sequenceName] = 0;
        }

        $this->lastInsertIds[$sequenceName]++;

        return "" . $this->lastInsertIds[$sequenceName];
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($statement)
    {
        return new Statement();
    }

    /**
     * {@inheritdoc}
     */
    public function query($statement)
    {
        return new Statement();
    }

    /**
     * {@inheritdoc}
     */
    public function quote($string, $parameterType = \PDO::PARAM_STR)
    {
        return $string;
    }

    /**
     * {@inheritdoc}
     */
    public function rollBack()
    {
        $this->inTransaction = false;
    }

    /**
     * @param boolean $shouldFailOnPurpose
     */
    public function setToFailOnPurpose($shouldFailOnPurpose)
    {
        $this->shouldFailOnPurpose = $shouldFailOnPurpose;
    }
} 