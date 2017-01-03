<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Databases;

/**
 * Defines the interface to be implemented by database drivers
 * A driver is simply any class that can make a database connection
 */
interface IDriver
{
    /**
     * Creates a connection to the input server
     *
     * @param Server $server The server to connect to
     * @param array $connectionOptions The list of connection options
     * @param array $driverOptions The list of driver options
     * @return IConnection The database connection
     */
    public function connect(Server $server, array $connectionOptions = [], array $driverOptions = []) : IConnection;
}