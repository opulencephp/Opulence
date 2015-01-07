<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the short option parser
 */
namespace RDev\Console\Requests\Parsers;

class ShortOptionTest extends \PHPUnit_Framework_TestCase 
{
    /** @var ShortOption The parser to use in tests */
    private $parser = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->parser = new ShortOption();
    }

    /**
     * Tests parsing an invalid short option
     */
    public function testParsingInvalidShortOption()
    {
        $this->setExpectedException("\\RuntimeException");
        $tokens = ["foo"];
        $this->parser->parse($tokens[0], $tokens);
    }

    /**
     * Tests parsing multiple options
     */
    public function testParsingMultipleOptions()
    {
        $tokens = ["-rad"];
        $option = $this->parser->parse($tokens[0], $tokens);
        $this->assertEquals([["r", null], ["a", null], ["d", null]], $option);
    }

    /**
     * Tests parsing single option
     */
    public function testParsingSingleOption()
    {
        $tokens = ["-r"];
        $option = $this->parser->parse($tokens[0], $tokens);
        $this->assertEquals([["r", null]], $option);
    }
}