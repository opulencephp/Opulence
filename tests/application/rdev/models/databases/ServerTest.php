<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the server
 */
namespace RDev\Models\Databases;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Server The mock to use in the tests */
    private $serverMock = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->serverMock = $this->getMockForAbstractClass("RDev\\Models\\Databases\\Server");
    }

    /**
     * Tests setting the host
     */
    public function testSettingHost()
    {
        $this->serverMock->setHost("127.0.0.1");
        $this->assertEquals("127.0.0.1", $this->serverMock->getHost());
    }

    /**
     * Tests setting the port
     */
    public function testSettingPort()
    {
        $this->serverMock->setPort(80);
        $this->assertEquals(80, $this->serverMock->getPort());
    }
} 