<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Databases\Tests;

use Opulence\Databases\Tests\Mocks\Server;

/**
 * Tests the relational database server
 */
class ServerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests setting the character set
     */
    public function testSettingCharset()
    {
        $charset = 'foo';
        $server = new Server();
        $server->setCharset($charset);
        $this->assertSame($charset, $server->getCharset());
    }

    /**
     * Tests setting the data in the constructor
     */
    public function testSettingDataInConstructor()
    {
        $server = new Server(
            '127.0.0.1',
            'username',
            'password',
            'dbname',
            123,
            'charset'
        );
        $this->assertSame('127.0.0.1', $server->getHost());
        $this->assertSame('username', $server->getUsername());
        $this->assertSame('password', $server->getPassword());
        $this->assertSame('dbname', $server->getDatabaseName());
        $this->assertSame(123, $server->getPort());
        $this->assertSame('charset', $server->getCharset());
    }

    /**
     * Tests setting the database name
     */
    public function testSettingDatabaseName()
    {
        $databaseName = 'dbname';
        $server = new Server();
        $server->setDatabaseName($databaseName);
        $this->assertSame($databaseName, $server->getDatabaseName());
    }

    /**
     * Tests setting the host
     */
    public function testSettingHost()
    {
        $server = new Server();
        $server->setHost('127.0.0.1');
        $this->assertSame('127.0.0.1', $server->getHost());
    }

    /**
     * Tests setting the password
     */
    public function testSettingPassword()
    {
        $password = 'bar';
        $server = new Server();
        $server->setPassword($password);
        $this->assertSame($password, $server->getPassword());
    }

    /**
     * Tests setting the port
     */
    public function testSettingPort()
    {
        $server = new Server();
        $server->setPort(80);
        $this->assertSame(80, $server->getPort());
    }

    /**
     * Tests setting the username
     */
    public function testSettingUsername()
    {
        $name = 'foo';
        $server = new Server();
        $server->setUsername($name);
        $this->assertSame($name, $server->getUsername());
    }
}
