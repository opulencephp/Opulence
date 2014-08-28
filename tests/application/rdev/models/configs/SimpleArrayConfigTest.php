<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the simple array config
 */
namespace RDev\Models\Configs;

class SimpleArrayConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests instantiating the config from an array
     */
    public function testFromArray()
    {
        $configArray = ["foo" => "bar"];
        $config = SimpleArrayConfig::fromArray($configArray);
        $this->assertEquals($configArray, $config->toArray());
    }

    /**
     * Tests converting to an array
     */
    public function testToArray()
    {
        $configArray = ["foo" => "bar"];
        $config = new SimpleArrayConfig($configArray);
        $this->assertEquals($configArray, $config->toArray());
    }
} 