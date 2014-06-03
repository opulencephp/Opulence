<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an extension of the PDO library with lazy-connection
 * In other words, a database connection is only made if we absolutely need to, which gives us a performance gain
 */
namespace RDev\Models\Databases\SQL;
use RDev\Models\Exceptions;

class RDevPDO extends \PDO
{
    /** The name of the PDOStatement class to use */
    const PDO_STATEMENT_CLASS = "RDevPDOStatement";

    /** @var Server The server we're connecting to */
    private $server = null;
    /** @var bool Whether or not we're connected */
    private $isConnected = false;
    /**
     * The number of transactions we're currently in
     * Useful for nested transactions
     *
     * @var int
     */
    private $transactionCounter = 0;

    /**
     * @param Server $server The server we're connecting to
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Nested transactions are permitted
     * {@inheritdoc}
     */
    public function beginTransaction()
    {
        $this->connect();

        if(!$this->transactionCounter++)
        {
            parent::beginTransaction();
        }
    }

    /**
     * If we are in a nested transaction and this isn't the final commit of the nested transactions, nothing happens
     * {@inheritdoc}
     */
    public function commit()
    {
        if(!--$this->transactionCounter)
        {
            parent::commit();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function errorCode()
    {
        $this->connect();

        return parent::errorCode();
    }

    /**
     * @inheritdoc
     */
    public function errorInfo()
    {
        $this->connect();

        return parent::errorInfo();
    }

    /**
     * {@inheritdoc}
     */
    public function exec($statement)
    {
        $this->connect();

        return parent::exec($statement);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($attribute)
    {
        $this->connect();

        return parent::getAttribute($attribute);
    }

    /**
     * @return Server
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
        $this->connect();

        return parent::inTransaction();
    }

    /**
     * {@inheritdoc}
     * @return RDevPDOStatement
     */
    public function prepare($statement, array $driverOptions = [])
    {
        $this->connect();

        return parent::prepare($statement, $driverOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function query($statement)
    {
        $this->connect();

        return parent::query($statement);
    }

    /**
     * {@inheritdoc}
     */
    public function quote($string, $parameterType = \PDO::PARAM_STR)
    {
        $this->connect();

        return parent::quote($string, $parameterType);
    }

    /**
     * {@inheritdoc}
     */
    public function rollBack()
    {
        if($this->transactionCounter >= 0)
        {
            parent::rollBack();
        }

        $this->transactionCounter = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($attribute, $value)
    {
        $this->connect();

        return parent::setAttribute($attribute, $value);
    }

    /**
     * Attempts to connect to the server, which is done via lazy-connecting
     *
     * @return bool True if we connected successfully, otherwise false
     */
    private function connect()
    {
        if($this->isConnected)
        {
            return true;
        }

        try
        {
            parent::__construct($this->server->getConnectionString(), $this->server->getUsername(), $this->server->getPassword());
            parent::setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            parent::setAttribute(\PDO::ATTR_STATEMENT_CLASS, [__NAMESPACE__ . "\\" . self::PDO_STATEMENT_CLASS, [$this]]);
            $this->isConnected = true;
        }
        catch(\Exception $ex)
        {
            Exceptions\Log::write("Unable to connect to server \"" . $this->server->getDisplayName() . "\" (" . $this->server->getHost() . ")");
            $this->isConnected = false;
        }

        return $this->isConnected;
    }
} 