<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the response class
 */
namespace RDev\Console\Responses;
use RDev\Console\Responses\Formatters\Elements;
use RDev\Tests\Console\Responses\Mocks;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /** @var Mocks\Response The response to use in tests */
    private $response = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->response = new Mocks\Response(new Compilers\Compiler());
    }

    /**
     * Tests clearing the response
     */
    public function testClearingResponse()
    {
        ob_start();
        $this->response->clear();
        $this->assertEquals(chr(27) . "[2J" . chr(27) . "[;H", ob_get_clean());
    }

    /**
     * Tests getting the built-in elements
     */
    public function testGettingBuiltInElements()
    {
        $expectedElements = [
            new Elements\Element("info", new Elements\Style("green")),
            new Elements\Element("error", new Elements\Style("black", "yellow")),
            new Elements\Element("fatal", new Elements\Style("white", "red")),
            new Elements\Element("question", new Elements\Style("white", "blue")),
            new Elements\Element("comment", new Elements\Style("yellow")),
            new Elements\Element("b", new Elements\Style(null, null, ["bold"])),
            new Elements\Element("u", new Elements\Style(null, null, ["underline"]))
        ];
        $this->assertEquals($expectedElements, $this->response->getElementRegistry()->getElements());
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