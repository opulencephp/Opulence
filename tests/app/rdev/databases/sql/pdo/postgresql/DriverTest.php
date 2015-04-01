<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the pgsql PDO driver
 */
namespace RDev\Databases\SQL\PDO\PostgreSQL;
use RDev\Databases\SQL;
use RDev\Tests\Databases\SQL\Mocks\Server;

class DriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests creating the DSN with an SSL mode specified
     */
    public function testCreatingDSNWithSSLMode()
    {
        $server = new Server();
        $driver = new Driver();
        $sslMode = "fakesslmode";
        $expectedResult = "pgsql:host=" . $server->getHost() . ";dbname=" . $server->getDatabaseName() . ";"
            . "port=" . $server->getPort() . ";options='--client_encoding=" . $server->getCharset() . "';"
            . "sslmode=" . $sslMode . ";";
        $this->assertEquals($expectedResult, $this->getDSN($driver, $server, ["sslmode" => $sslMode]));
    }

    /**
     * Tests creating the DSN without an SSL mode specified
     */
    public function testCreatingDSNWithoutSSLMode()
    {
        $server = new Server();
        $driver = new Driver();
        $expectedResult = "pgsql:host=" . $server->getHost() . ";dbname=" . $server->getDatabaseName() . ";"
            . "port=" . $server->getPort() . ";options='--client_encoding=" . $server->getCharset() . "';";
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
        $method = $class->getMethod("getDSN");
        $method->setAccessible(true);

        return $method->invokeArgs($driver, [$server, $connectionOptions, $connectionOptions]);
    }
} 