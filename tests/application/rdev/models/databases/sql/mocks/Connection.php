<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the connection class for use in testing
 */
namespace RDev\Tests\Models\Databases\SQL\Mocks;
use RDev\Models\Databases\SQL;
use RDev\Models\Databases\SQL\Systems;

class Connection implements SQL\IConnection
{
    /** @var Systems\System The system used by this connection */
    private $system = null;
    /** @var SQL\Server The server to connect to */
    private $server = null;
    /** @var bool Whether or not we're in a transaction */
    private $inTransaction = false;

    /**
     * @param SQL\Server $server The server to connect to
     */
    public function __construct(SQL\Server $server)
    {
        $this->system = new Systems\System();
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
    public function getDatabaseSystem()
    {
        return $this->system;
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
    public function inTransaction()
    {
        return $this->inTransaction;
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
} 