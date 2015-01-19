<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the framework paths
 */
namespace RDev\Framework;

class PathsTest extends \PHPUnit_Framework_TestCase 
{
    /**
     * Tests constructing with an empty array
     */
    public function testConstructingWithEmptyArray()
    {
        $paths = new Paths([]);
        $this->assertNull($paths["foo"]);
    }

    /**
     * Tests setting a null offset
     */
    public function testSettingNullOffset()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $paths = new Paths([]);
        $paths[] = "foo";
    }
}