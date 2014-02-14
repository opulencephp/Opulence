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
 * Tests our cache server
 */
namespace RamODev\Storage\NoSQL\Redis;

require_once(__DIR__ . "/../../../../storage/nosql/redis/Server.php");

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests setting the host
     */
    public function testSettingHost()
    {
        $ip = '127.0.0.1';
        $server = $this->getMockForAbstractClass("RamODev\\Storage\\NoSQL\\Redis\\Server");
        $server->setHost($ip);

        $this->assertEquals($ip, $server->getHost());
    }

    /**
     * Tests setting the lifetime
     */
    public function testSettingLifetime()
    {
        $lifetime = 12345;
        $server = $this->getMockForAbstractClass("RamODev\\Storage\\NoSQL\\Redis\\Server");
        $server->setLifetime($lifetime);

        $this->assertEquals($lifetime, $server->getLifetime());
    }

    /**
     * Tests setting the display name
     */
    public function testSettingDisplayName()
    {
        $displayName = 'nicename';
        $server = $this->getMockForAbstractClass("RamODev\\Storage\\NoSQL\\Redis\\Server");
        $server->setDisplayName($displayName);

        $this->assertEquals($displayName, $server->getDisplayName());
    }

    /**
     * Tests setting the port
     */
    public function testSettingPort()
    {
        $port = 11211;
        $server = $this->getMockForAbstractClass("RamODev\\Storage\\NoSQL\\Redis\\Server");
        $server->setPort($port);

        $this->assertEquals($port, $server->getPort());
    }
} 