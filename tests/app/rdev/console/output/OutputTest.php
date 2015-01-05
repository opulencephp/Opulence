<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the output class
 */
namespace RDev\Console\Output;
use RDev\Tests\Console\Output\Mocks;

class OutputTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Mocks\Output The output to use in tests */
    private $output = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->output = new Mocks\Output();
    }

    /**
     * Tests writing multiple messages with new lines
     */
    public function testWritingMultipleMessagesWithNewLines()
    {
        ob_start();
        $this->output->writeln(["foo", "bar"]);
        $this->assertEquals("foo" . PHP_EOL . "bar" . PHP_EOL, ob_get_clean());
    }

    /**
     * Tests writing multiple messages with no new lines
     */
    public function testWritingMultipleMessagesWithNoNewLines()
    {
        ob_start();
        $this->output->write(["foo", "bar"]);
        $this->assertEquals("foobar", ob_get_clean());
    }

    /**
     * Tests writing a single message with a new line
     */
    public function testWritingSingleMessageWithNewLine()
    {
        ob_start();
        $this->output->writeln("foo");
        $this->assertEquals("foo" . PHP_EOL, ob_get_clean());
    }

    /**
     * Tests writing a single message with no new line
     */
    public function testWritingSingleMessageWithNoNewLine()
    {
        ob_start();
        $this->output->write("foo");
        $this->assertEquals("foo", ob_get_clean());
    }
}