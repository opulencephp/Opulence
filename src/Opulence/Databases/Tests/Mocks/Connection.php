<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\Tests\Mocks;

use Exception;
use Opulence\Databases\IConnection;
use Opulence\Databases\Providers\Provider;
use Opulence\Databases\Server as RealServer;
use PDO;

/**
 * Mocks the connection class for use in testing
 */
class Connection implements IConnection
{
    /** @var Provider The provider used by this connection */
    private Provider $provider;
    /** @var RealServer The server to connect to */
    private RealServer $server;
    /** @var bool Whether or not we're in a transaction */
    private bool $inTransaction = false;
    /** @var array The mapping of sequence names to last insert Ids */
    private array $lastInsertIds = [];
    /** @var bool Whether or not this connection should fail on purpose */
    private bool $shouldFailOnPurpose = false;

    /**
     * @param RealServer $server The server to connect to
     */
    public function __construct(RealServer $server)
    {
        $this->provider = new Provider();
        $this->server = $server;
    }

    /**
     * @inheritdoc
     */
    public function beginTransaction()
    {
        $this->inTransaction = true;
    }

    /**
     * @inheritdoc
     */
    public function commit()
    {
        $this->inTransaction = false;

        if ($this->shouldFailOnPurpose) {
            throw new Exception('Commit failed');
        }
    }

    /**
     * @inheritdoc
     */
    public function errorCode()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function errorInfo()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function exec($statement)
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function getDatabaseProvider(): Provider
    {
        return $this->provider;
    }

    /**
     * @inheritdoc
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @inheritdoc
     */
    public function inTransaction()
    {
        return $this->inTransaction;
    }

    /**
     * @inheritdoc
     */
    public function lastInsertId($sequenceName = null)
    {
        if (!isset($this->lastInsertIds[$sequenceName])) {
            $this->lastInsertIds[$sequenceName] = 0;
        }

        $this->lastInsertIds[$sequenceName]++;

        return '' . $this->lastInsertIds[$sequenceName];
    }

    /**
     * @inheritdoc
     */
    public function prepare($statement)
    {
        return new Statement();
    }

    /**
     * @inheritdoc
     */
    public function query($statement)
    {
        return new Statement();
    }

    /**
     * @inheritdoc
     */
    public function quote($string, $parameterType = PDO::PARAM_STR)
    {
        return $string;
    }

    /**
     * @inheritdoc
     */
    public function rollBack()
    {
        $this->inTransaction = false;
    }

    /**
     * @param bool $shouldFailOnPurpose
     */
    public function setToFailOnPurpose($shouldFailOnPurpose): void
    {
        $this->shouldFailOnPurpose = $shouldFailOnPurpose;
    }
}
