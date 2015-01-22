<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the silent response
 */
namespace RDev\Console\Responses;

class SilentTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Silent The response to use in tests */
    private $response = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->response = new Silent(new Compilers\Compiler());
    }

    /**
     * Tests writing without a new line
     */
    public function testWrite()
    {
        ob_start();
        $this->response->write("foo");
        $this->assertEmpty(ob_get_clean());
    }

    /**
     * Tests writing with a new line
     */
    public function testWriteln()
    {
        ob_start();
        $this->response->writeln("foo");
        $this->assertEmpty(ob_get_clean());
    }
}