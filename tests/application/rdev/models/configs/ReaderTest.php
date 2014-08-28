<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the config reader
 */
namespace RDev\Models\Configs;
use RDev\Tests\Models\Configs\Mocks;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var Mocks\Reader The reader to use in the tests */
    private $reader = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->reader = new Mocks\Reader();
    }

    /**
     * Tests checking for matching required fields
     */
    public function testCheckingForMatchingRequiredFields()
    {
        $this->assertTrue($this->reader->hasRequiredFields([
            "foo",
        ], [
            "foo"
        ]));
        $this->assertTrue($this->reader->hasRequiredFields([
            "foo",
            "bar",
            "nested" => [
                "nestedNested" => [
                    "blah",
                    "notMissing"
                ]
            ]
        ], [
            "foo",
            "bar",
            "nested" => [
                "nestedNested" => [
                    "blah",
                    "notMissing"
                ]
            ]
        ]));
    }

    /**
     * Tests checking for a missing required field
     */
    public function testCheckingForMissingRequiredFields()
    {
        $this->assertFalse($this->reader->hasRequiredFields([
            "foo" => null,
        ], [
            "bar" => null
        ]));
        $this->assertFalse($this->reader->hasRequiredFields([
            "foo",
            "bar",
            "nested" => [
                "nestedNested" => [
                    "blah"
                ]
            ]
        ], [
            "foo",
            "bar",
            "nested" => [
                "nestedNested" => [
                    "blah",
                    "missing"
                ]
            ]
        ]));
    }

    /**
     * Tests using a config file with an invalid extension
     */
    public function testUsingConfigFileWithInvalidExtension()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->reader->load("RDev\\Tests\\Models\\Configs\\Config", __DIR__ . "/config.txt");
    }

    /**
     * Tests using an invalid JSON file
     */
    public function testUsingInvalidJSONConfigFile()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->reader->load("RDev\\Tests\\Models\\Configs\\Config", __DIR__ . "/invalidJSONConfig.json");
    }
} 