<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the silent response
 */
namespace Opulence\Console\Responses;

class SilentResponseTest extends \PHPUnit_Framework_TestCase
{
    /** @var SilentResponse The response to use in tests */
    private $response = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->response = new SilentResponse();
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