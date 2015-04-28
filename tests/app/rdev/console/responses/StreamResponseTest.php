<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the stream response
 */
namespace RDev\Console\Responses;
use RDev\Console\Responses\Compilers\Compiler;
use RDev\Console\Responses\Compilers\Lexers\Lexer;
use RDev\Console\Responses\Compilers\Parsers\Parser;

class StreamResponseTest extends \PHPUnit_Framework_TestCase
{
    /** @var StreamResponse The response to use in tests */
    private $response = null;
    /** @var Compiler The compiler to use in tests */
    private $compiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->compiler = new Compiler(new Lexer(), new Parser());
        $this->response = new StreamResponse(fopen("php://memory", "w"), $this->compiler);
    }

    /**
     * Tests getting the stream
     */
    public function testGettingStream()
    {
        $this->assertTrue(is_resource($this->response->getStream()));
    }

    /**
     * Tests an invalid stream
     */
    public function testInvalidStream()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        new StreamResponse("foo", $this->compiler);
    }

    /**
     * Test writing an array message
     */
    public function testWriteOnArray()
    {
        $this->response->write(["foo", "bar"]);
        rewind($this->response->getStream());
        $this->assertEquals("foobar", stream_get_contents($this->response->getStream()));
    }

    /**
     * Tests writing a string message
     */
    public function testWriteOnString()
    {
        $this->response->write("foo");
        rewind($this->response->getStream());
        $this->assertEquals("foo", stream_get_contents($this->response->getStream()));
    }

    /**
     * Test writing an array message to a line
     */
    public function testWritelnOnArray()
    {
        $this->response->writeln(["foo", "bar"]);
        rewind($this->response->getStream());
        $this->assertEquals("foo" . PHP_EOL . "bar" . PHP_EOL, stream_get_contents($this->response->getStream()));
    }

    /**
     * Tests writing a string message to a line
     */
    public function testWritelnOnString()
    {
        $this->response->writeln("foo");
        rewind($this->response->getStream());
        $this->assertEquals("foo" . PHP_EOL, stream_get_contents($this->response->getStream()));
    }
}