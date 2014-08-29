<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the simple config
 */
namespace RDev\Models\Configs;
use RDev\Tests\Models\Configs\Mocks;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests using the config as an array
     */
    public function testAsArrayAccess()
    {
        $config = new Config();
        $config["foo"] = "bar";
        $this->assertEquals("bar", $config["foo"]);
        $this->assertTrue(isset($config["foo"]));
        unset($config["foo"]);
        $this->assertFalse(isset($config["foo"]));
        $config[] = "blah";
        $this->assertEquals("blah", $config[0]);
    }

    /**
     * Tests checking for matching required fields
     */
    public function testCheckingForMatchingRequiredFields()
    {
        $config = new Mocks\Config([
            "foo"
        ]);
        $config->setRequiredFields([
            "foo"
        ]);
        $this->assertTrue($config->isValid());
        $config->fromArray([
            "foo",
            "bar",
            "nested" => [
                "nestedNested" => [
                    "blah",
                    "notMissing"
                ]
            ]
        ]);
        $config->setRequiredFields([
            "foo",
            "bar",
            "nested" => [
                "nestedNested" => [
                    "blah",
                    "notMissing"
                ]
            ]
        ]);
        $this->assertTrue($config->isValid());
    }

    /**
     * Tests checking for a missing required field
     */
    public function testCheckingForMissingRequiredFields()
    {
        $config = new Mocks\Config([
            "foo" => null
        ]);
        $config->setRequiredFields([
            "bar" => null
        ]);
        $this->assertFalse($config->isValid());
        $config->fromArray([
            "foo",
            "bar",
            "nested" => [
                "nestedNested" => [
                    "blah"
                ]
            ]
        ]);
        $config->setRequiredFields([
            "foo",
            "bar",
            "nested" => [
                "nestedNested" => [
                    "blah",
                    "missing"
                ]
            ]
        ]);
        $this->assertFalse($config->isValid());
    }

    /**
     * Tests checking if the config is valid
     */
    public function testIsValid()
    {
        $config = new Config();
        $this->assertTrue($config->isValid());
    }

    /**
     * Tests converting to an array
     */
    public function testToArray()
    {
        $configArray = ["foo" => "bar"];
        $configWithArrayInConstructor = new Config($configArray);
        $configWithArrayInFromArray = new Config();
        $configWithArrayInFromArray->fromArray($configArray);
        $this->assertEquals($configArray, $configWithArrayInConstructor->toArray());
        $this->assertEquals($configArray, $configWithArrayInFromArray->toArray());
    }
} 