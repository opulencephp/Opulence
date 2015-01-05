<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the response class
 */
namespace RDev\Console\Responses;
use RDev\Tests\Console\Responses\Mocks;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var Mocks\Response The response to use in tests */
    private $response = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->response = new Mocks\Response();
    }

    /**
     * Tests writing multiple messages with new lines
     */
    public function testWritingMultipleMessagesWithNewLines()
    {
        ob_start();
        $this->response->writeln(["foo", "bar"]);
        $this->assertEquals("foo" . PHP_EOL . "bar" . PHP_EOL, ob_get_clean());
    }

    /**
     * Tests writing multiple messages with no new lines
     */
    public function testWritingMultipleMessagesWithNoNewLines()
    {
        ob_start();
        $this->response->write(["foo", "bar"]);
        $this->assertEquals("foobar", ob_get_clean());
    }

    /**
     * Tests writing a single message with a new line
     */
    public function testWritingSingleMessageWithNewLine()
    {
        ob_start();
        $this->response->writeln("foo");
        $this->assertEquals("foo" . PHP_EOL, ob_get_clean());
    }

    /**
     * Tests writing a single message with no new line
     */
    public function testWritingSingleMessageWithNoNewLine()
    {
        ob_start();
        $this->response->write("foo");
        $this->assertEquals("foo", ob_get_clean());
    }
}