<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the host
 */
namespace RDev\Applications\Environments\Hosts;

class HostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding a host
     */
    public function testAddingHost()
    {
        $host = new Host("localhost", false);
        $this->assertEquals("localhost", $host->getHost());
        $this->assertFalse($host->usesRegex());
    }

    /**
     * Tests adding a regex
     */
    public function testAddingRegex()
    {
        $host = new Host(".*", true);
        $this->assertEquals(".*", $host->getHost());
        $this->assertTrue($host->usesRegex());
    }
}