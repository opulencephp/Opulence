<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\TestsTemp\Adapters\Pdo\PostgreSql;

use Opulence\Databases\Adapters\Pdo\PostgreSql\Driver;
use Opulence\Databases\TestsTemp\Mocks\Server;

/**
 * Tests the pgsql PDO driver
 */
class DriverTest extends \PHPUnit\Framework\TestCase
{
    public function testCreatingDSNWithSSLMode(): void
    {
        $server = new Server();
        $driver = new Driver();
        $sslMode = 'fakesslmode';
        $expectedResult = 'pgsql:host=' . $server->getHost() . ';dbname=' . $server->getDatabaseName() . ';'
            . 'port=' . $server->getPort() . ";options='--client_encoding=" . $server->getCharset() . "';"
            . 'sslmode=' . $sslMode . ';';
        $this->assertEquals($expectedResult, $this->getDSN($driver, $server, ['sslmode' => $sslMode]));
    }

    public function testCreatingDSNWithoutSSLMode(): void
    {
        $server = new Server();
        $driver = new Driver();
        $expectedResult = 'pgsql:host=' . $server->getHost() . ';dbname=' . $server->getDatabaseName() . ';'
            . 'port=' . $server->getPort() . ";options='--client_encoding=" . $server->getCharset() . "';";
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
    private function getDSN(Driver $driver, Server $server, array $connectionOptions = []): string
    {
        $class = new \ReflectionClass(get_class($driver));
        $method = $class->getMethod('getDSN');
        $method->setAccessible(true);

        return $method->invokeArgs($driver, [$server, $connectionOptions, $connectionOptions]);
    }
}
