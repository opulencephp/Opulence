<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Databases\Adapters\Pdo;

use Opulence\Databases\IConnection;
use Opulence\Databases\IDriver;
use Opulence\Databases\Providers\Provider;
use Opulence\Databases\Server;

/**
 * Defines the base class for PDO drivers
 */
abstract class Driver implements IDriver
{
    /** @var Provider The provider this driver uses */
    protected $provider = null;

    public function __construct()
    {
        $this->setProvider();
    }

    /**
     * @inheritdoc
     * @return Connection The PDO connection
     */
    public function connect(Server $server, array $connectionOptions = [], array $driverOptions = []) : IConnection
    {
        $dsn = $this->getDsn($server, $connectionOptions);

        return new Connection($this->provider, $server, $dsn, $driverOptions);
    }

    /**
     * Gets the DSN string to connect to a server through PDO
     *
     * @param Server $server The server to connect to
     * @param array $options The list of driver-specific options
     * @return string The DSN to use to connect to PDO
     */
    abstract protected function getDsn(Server $server, array $options = []) : string;

    /**
     * Sets the provider used by this driver's connections
     */
    abstract protected function setProvider();
}
