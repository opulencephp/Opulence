<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the relational database server
 */
namespace RDev\Models\Databases\SQL;
use RDev\Tests\Models\Databases\SQL\Mocks;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests setting the character set
     */
    public function testSettingCharset()
    {
        $charset = 'foo';
        $server = new Mocks\Server();
        $server->setCharset($charset);
        $this->assertEquals($charset, $server->getCharset());
    }

    /**
     * Tests setting the database name
     */
    public function testSettingDatabaseName()
    {
        $databaseName = 'dbname';
        $server = new Mocks\Server();
        $server->setDatabaseName($databaseName);
        $this->assertEquals($databaseName, $server->getDatabaseName());
    }

    /**
     * Tests setting the host
     */
    public function testSettingHost()
    {
        $host = '127.0.0.1';
        $server = new Mocks\Server();
        $server->setHost($host);
        $this->assertEquals($host, $server->getHost());
    }

    /**
     * Tests setting the password
     */
    public function testSettingPassword()
    {
        $password = 'bar';
        $server = new Mocks\Server();
        $server->setPassword($password);
        $this->assertEquals($password, $server->getPassword());
    }

    /**
     * Tests setting the username
     */
    public function testSettingUsername()
    {
        $name = 'foo';
        $server = new Mocks\Server();
        $server->setUsername($name);
        $this->assertEquals($name, $server->getUsername());
    }
}