<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Databases\Tests\Adapters\Pdo\MySql;

use Opulence\Databases\Adapters\Pdo\MySql\Driver;
use Opulence\Databases\Tests\Mocks\Server;

/**
 * Tests the MySQL PDO driver
 */
class DriverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests creating the DSN with a Unix socket specified
     */
    public function testCreatingDSNWithUnixSocket()
    {
        $server = new Server();
        $driver = new Driver();
        $unixSocket = 'fakesocket';
        $expectedResult = 'mysql:host=' . $server->getHost() . ';dbname=' . $server->getDatabaseName() . ';'
            . 'port=' . $server->getPort() . ';charset=' . $server->getCharset() . ';'
            . 'unix_socket=' . $unixSocket . ';';
        $this->assertEquals($expectedResult, $this->getDSN($driver, $server, ['unix_socket' => $unixSocket]));
    }

    /**
     * Tests creating the DSN without a Unix socket specified
     */
    public function testCreatingDSNWithoutUnixSocket()
    {
        $server = new Server();
        $driver = new Driver();
        $expectedResult = 'mysql:host=' . $server->getHost() . ';dbname=' . $server->getDatabaseName() . ';'
            . 'port=' . $server->getPort() . ';charset=' . $server->getCharset() . ';';
        $this->assertEquals($expectedResult, $this->getDSN($driver, $server));
    }

    /**
     * Gets the DSN for the input driver and server
     *
     * @param Driver $driver The driver to connect with
     * @param Server $server The server to connect to
     * @param array $connectionOptions The connection options
     * @return string The DSN for the input driver and server
     */
    private function getDSN(Driver $driver, Server $server, array $connectionOptions = [])
    {
        $class = new \ReflectionClass(get_class($driver));
        $method = $class->getMethod('getDSN');
        $method->setAccessible(true);

        return $method->invokeArgs($driver, [$server, $connectionOptions, $connectionOptions]);
    }
}
