<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 *
 *
 * Performs tests for our server class
 */
namespace RamODev\Databases\RDBMS;

require_once(__DIR__ . "/../../../databases/rdbms/Server.php");

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests setting the database name
     */
    public function testSettingDatabaseName()
    {
        $databaseName = 'dbname';
        $server = $this->getMockForAbstractClass("RamODev\\Databases\\RDBMS\\Server");
        $server->setDatabaseName($databaseName);
        $this->assertEquals($databaseName, $server->getDatabaseName());
    }

    /**
     * Tests setting the host
     */
    public function testSettingHost()
    {
        $ip = '127.0.0.1';
        $server = $this->getMockForAbstractClass("RamODev\\Databases\\RDBMS\\Server");
        $server->setHost($ip);
        $this->assertEquals($ip, $server->getHost());
    }

    /**
     * Tests setting the display name
     */
    public function testSettingDisplayName()
    {
        $displayName = 'nicename';
        $server = $this->getMockForAbstractClass("RamODev\\Databases\\RDBMS\\Server");
        $server->setDisplayName($displayName);
        $this->assertEquals($displayName, $server->getDisplayName());
    }

    /**
     * Tests setting the password
     */
    public function testSettingPassword()
    {
        $password = 'bar';
        $server = $this->getMockForAbstractClass("RamODev\\Databases\\RDBMS\\Server");
        $server->setPassword($password);
        $this->assertEquals($password, $server->getPassword());
    }

    /**
     * Tests setting the username
     */
    public function testSettingUsername()
    {
        $name = 'foo';
        $server = $this->getMockForAbstractClass("RamODev\\Databases\\RDBMS\\Server");
        $server->setUsername($name);
        $this->assertEquals($name, $server->getUsername());
    }
}
 