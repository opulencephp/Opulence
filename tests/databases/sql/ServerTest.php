<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the relational database server
 */
namespace RamODev\Databases\SQL;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests setting the database name
     */
    public function testSettingDatabaseName()
    {
        $databaseName = 'dbname';
        $server = $this->getMockForAbstractClass("RamODev\\Databases\\SQL\\Server");
        $server->setDatabaseName($databaseName);
        $this->assertEquals($databaseName, $server->getDatabaseName());
    }

    /**
     * Tests setting the host
     */
    public function testSettingHost()
    {
        $ip = '127.0.0.1';
        $server = $this->getMockForAbstractClass("RamODev\\Databases\\SQL\\Server");
        $server->setHost($ip);
        $this->assertEquals($ip, $server->getHost());
    }

    /**
     * Tests setting the display name
     */
    public function testSettingDisplayName()
    {
        $displayName = 'nicename';
        $server = $this->getMockForAbstractClass("RamODev\\Databases\\SQL\\Server");
        $server->setDisplayName($displayName);
        $this->assertEquals($displayName, $server->getDisplayName());
    }

    /**
     * Tests setting the password
     */
    public function testSettingPassword()
    {
        $password = 'bar';
        $server = $this->getMockForAbstractClass("RamODev\\Databases\\SQL\\Server");
        $server->setPassword($password);
        $this->assertEquals($password, $server->getPassword());
    }

    /**
     * Tests setting the username
     */
    public function testSettingUsername()
    {
        $name = 'foo';
        $server = $this->getMockForAbstractClass("RamODev\\Databases\\SQL\\Server");
        $server->setUsername($name);
        $this->assertEquals($name, $server->getUsername());
    }
}
 