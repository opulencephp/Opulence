<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the view config
 */
namespace RDev\Views\Configs;
use RDev\Views;
use RDev\Views\Cache;

class ViewConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests an invalid GC chance
     */
    public function testInvalidGCChance()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "templates" => [
                "gcChance" => 1.5,
                "cachePath" => "/tmp"
            ]
        ];
        new ViewConfig($configArray);
    }

    /**
     * Tests an invalid GC total
     */
    public function testInvalidGCTotal()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "templates" => [
                "gcTotal" => 1.5,
                "cachePath" => "/tmp"
            ]
        ];
        new ViewConfig($configArray);
    }

    /**
     * Tests not specifying cache path
     */
    public function testNotSpecifyingCachePath()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "templates" => []
        ];
        new ViewConfig($configArray);
    }

    /**
     * Tests not specifying defaults
     */
    public function testNotSpecifyingDefaults()
    {
        $configArray = [
            "templates" => [
                "cachePath" => "/tmp"
            ]
        ];
        $config = new ViewConfig($configArray);
        $this->assertEquals(Cache\ICache::DEFAULT_GC_CHANCE, $config["templates"]["gcChance"]);
        $this->assertEquals(Cache\ICache::DEFAULT_GC_TOTAL, $config["templates"]["gcTotal"]);
    }

    /**
     * Tests not specifying template options
     */
    public function testNotSpecifyingTemplateOptions()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [];
        new ViewConfig($configArray);
    }

    /**
     * Tests specifying an invalid class for the cache
     */
    public function testSpecifyingInvalidClassForCache()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "templates" => [
                "cachePath" => "/tmp",
                "cache" => get_class($this)
            ]
        ];
        new ViewConfig($configArray);
    }

    /**
     * Tests specifying an invalid object for the cache
     */
    public function testSpecifyingInvalidObjectForCache()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = [
            "templates" => [
                "cachePath" => "/tmp",
                "cache" => $this
            ]
        ];
        new ViewConfig($configArray);
    }
}