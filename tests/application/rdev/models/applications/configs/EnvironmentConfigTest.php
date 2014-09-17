<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the environment config
 */
namespace RDev\Models\Applications\Configs;

class EnvironmentConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests a config with a callback
     */
    public function testConfigWithCallback()
    {
        $configArray = [
            function ()
            {
                return "staging";
            }
        ];
        $config = new EnvironmentConfig($configArray);
        $this->assertEquals($configArray, $config->toArray());
    }

    /**
     * Tests not setting anything
     */
    public function testNotSettingAnything()
    {
        $config = new EnvironmentConfig([]);
        $this->assertEquals([], $config->toArray());
    }

    /**
     * Tests passing an invalid development option
     */
    public function testPassingInvalidDevelopmentOption()
    {
        $this->setExpectedException("\\RuntimeException");
        new EnvironmentConfig(["development" => 1]);
    }

    /**
     * Tests passing an invalid list of development machines
     */
    public function testPassingInvalidListOfDevelopmentMachines()
    {
        $this->setExpectedException("\\RuntimeException");
        new EnvironmentConfig(["development" => [1, 2]]);
    }

    /**
     * Tests passing an invalid list of production machines
     */
    public function testPassingInvalidListOfProductionMachines()
    {
        $this->setExpectedException("\\RuntimeException");
        new EnvironmentConfig(["production" => [1, 2]]);
    }

    /**
     * Tests passing an invalid list of staging machines
     */
    public function testPassingInvalidListOfStagingMachines()
    {
        $this->setExpectedException("\\RuntimeException");
        new EnvironmentConfig(["staging" => [1, 2]]);
    }

    /**
     * Tests passing an invalid production option
     */
    public function testPassingInvalidProductionOption()
    {
        $this->setExpectedException("\\RuntimeException");
        new EnvironmentConfig(["production" => 1]);
    }

    /**
     * Tests passing an invalid staging option
     */
    public function testPassingInvalidStagingOption()
    {
        $this->setExpectedException("\\RuntimeException");
        new EnvironmentConfig(["staging" => 1]);
    }
} 