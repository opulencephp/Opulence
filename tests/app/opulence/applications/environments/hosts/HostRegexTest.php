<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the host regex
 */
namespace Opulence\Applications\Environments\Hosts;

class HostRegexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that delimiters are added
     */
    public function testDelimitersAreAdded()
    {
        $host = new HostRegex(".*");
        $this->assertEquals("#.*#", $host->getValue());
    }
}