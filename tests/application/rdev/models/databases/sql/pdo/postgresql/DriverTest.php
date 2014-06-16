<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the MySQL PDO driver
 */
namespace RDev\Models\Databases\SQL\PDO\PostgreSQL;
use RDev\Models\Databases\SQL;
use RDev\Tests\Models\Databases\SQL\Mocks;

class DriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests creating the DSN with an SSL mode specified
     */
    public function testsCreatingDSNWithSSLMode()
    {
        $server = new Mocks\Server();
        $driver = new Driver();
        $sslMode = "fakesslmode";
        $expectedResult = "mysql: host=" . $server->getHost() . ";dbname=" . $server->getDatabaseName() . ";"
            . "port=" . $server->getPort() . ";options='--client_encoding=" . $server->getCharset() . "';"
            . "sslmode=" . $sslMode . ";";
        $this->assertEquals($expectedResult, $this->getDSN($driver, $server, ["sslmode" => $sslMode]));
    }

    /**
     * Tests creating the DSN without an SSL mode specified
     */
    public function testsCreatingDSNWithoutSSLMode()
    {
        $server = new Mocks\Server();
        $driver = new Driver();
        $expectedResult = "mysql: host=" . $server->getHost() . ";dbname=" . $server->getDatabaseName() . ";"
            . "port=" . $server->getPort() . ";options='--client_encoding=" . $server->getCharset() . "';";
        $this->assertEquals($expectedResult, $this->getDSN($driver, $server));
    }

    /**
     * Gets the DSN for the input driver and server
     *
     * @param Driver $driver The driver to connect with
     * @param SQL\Server $server The server to connect to
     * @param array $connectionOptions The connection options
     * @return string The DSN for the input driver and server
     */
    private function getDSN(Driver $driver, SQL\Server $server, array $connectionOptions = [])
    {
        $class = new \ReflectionClass(get_class($driver));
        $method = $class->getMethod("createDSN");
        $method->setAccessible(true);

        return $method->invokeArgs($driver, [$server, $connectionOptions, $connectionOptions]);
    }
} 