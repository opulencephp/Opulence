<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the application config
 */
namespace RDev\Models\Applications\Configs;

class ApplicationConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that the environment key is automatically set
     */
    public function testEnvironmentKeyIsSetAutomatically()
    {
        $config = new ApplicationConfig([]);
        $this->assertEquals([], $config["environment"]);
    }
} 