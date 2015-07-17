<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the host name
 */
namespace Opulence\Applications\Environments\Hosts;

class HostNameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the value
     */
    public function testGettingValue()
    {
        $host = new HostName("localhost");
        $this->assertEquals("localhost", $host->getValue());
    }
}