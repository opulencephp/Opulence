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
     * Tests checking for matching required fields
     */
    public function testCheckingForMatchingRequiredFields()
    {
        new Mocks\Config([
            "foo"
        ], [
            "foo"
        ]);
        new Mocks\Config([
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
        ]);
        // This is a little hack just to make sure all the above code executed ok
        $this->assertTrue(true);
    }

    /**
     * Tests checking for a missing required field
     */
    public function testCheckingForMissingRequiredFields()
    {
        $exceptionsThrown = false;

        try
        {
            new Mocks\Config([
                "foo" => null
            ], [
                "bar" => null
            ]);
        }
        catch(\RuntimeException $ex)
        {
            $exceptionsThrown = true;
        }

        try
        {
            new Mocks\Config([
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
            ]);
        }
        catch(\RuntimeException $ex)
        {
            $exceptionsThrown = $exceptionsThrown && true;
        }

        $this->assertTrue($exceptionsThrown);
    }

    /**
     * Tests getting the count of items in the config
     */
    public function testCount()
    {
        $configArray = [
            "foo",
            "bar"
        ];
        $config = new Config($configArray);
        $this->assertEquals(count($configArray), $config->count());
    }

    /**
     * Tests making sure the config can be treated like an array
     */
    public function testIsArrayObject()
    {
        $this->assertInstanceOf("\\ArrayObject", new Config);
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