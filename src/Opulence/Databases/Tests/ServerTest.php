<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

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
    public function testSettingCharset(): void
    {
        $charset = 'foo';
        $server = new Server();
        $server->setCharset($charset);
        $this->assertEquals($charset, $server->getCharset());
    }

    /**
     * Tests setting the data in the constructor
     */
    public function testSettingDataInConstructor(): void
    {
        $server = new Server(
            '127.0.0.1',
            'username',
            'password',
            'dbname',
            123,
            'charset'
        );
        $this->assertEquals('127.0.0.1', $server->getHost());
        $this->assertEquals('username', $server->getUsername());
        $this->assertEquals('password', $server->getPassword());
        $this->assertEquals('dbname', $server->getDatabaseName());
        $this->assertEquals(123, $server->getPort());
        $this->assertEquals('charset', $server->getCharset());
    }

    /**
     * Tests setting the database name
     */
    public function testSettingDatabaseName(): void
    {
        $databaseName = 'dbname';
        $server = new Server();
        $server->setDatabaseName($databaseName);
        $this->assertEquals($databaseName, $server->getDatabaseName());
    }

    /**
     * Tests setting the host
     */
    public function testSettingHost(): void
    {
        $server = new Server();
        $server->setHost('127.0.0.1');
        $this->assertEquals('127.0.0.1', $server->getHost());
    }

    /**
     * Tests setting the password
     */
    public function testSettingPassword(): void
    {
        $password = 'bar';
        $server = new Server();
        $server->setPassword($password);
        $this->assertEquals($password, $server->getPassword());
    }

    /**
     * Tests setting the port
     */
    public function testSettingPort(): void
    {
        $server = new Server();
        $server->setPort(80);
        $this->assertEquals(80, $server->getPort());
    }

    /**
     * Tests setting the username
     */
    public function testSettingUsername(): void
    {
        $name = 'foo';
        $server = new Server();
        $server->setUsername($name);
        $this->assertEquals($name, $server->getUsername());
    }
}
